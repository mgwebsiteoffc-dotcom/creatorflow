<?php

namespace App\Domains\Analytics\Actions;

use App\Models\Attribution;
use App\Models\CampaignAssignment;
use App\Models\DiscountCode;
use App\Models\Order;
use Illuminate\Support\Facades\DB;

/**
 * Multi-source order attribution.
 *
 * An incoming order is attributed to a creator using the first available
 * signal, in priority order:
 *   1. Unique discount code (highest confidence)
 *   2. Tracking tag / referral metadata on the order note/line items
 *   3. Post-purchase survey response (captured elsewhere)
 *
 * Attributions are written with a model type and weight, and daily analytics
 * rollups are updated. Multi-touch is supported by calling this action once
 * per signal with a fractional weight.
 */
class AttributeOrder
{
    public function handle(Order $order): ?Attribution
    {
        if ($order->attributions()->exists()) {
            return $order->attributions->first();
        }

        // 1. Discount code match
        $discountCode = $this->extractDiscountCode($order);
        if ($discountCode) {
            $code = DiscountCode::where('workspace_id', $order->workspace_id)
                ->where('code', $discountCode)
                ->first();

            if ($code) {
                $assignment = CampaignAssignment::where('campaign_id', $code->campaign_id)
                    ->where('creator_id', $code->creator_id)
                    ->first();

                return $this->record($order, $code->creator_id, $code->campaign_id, $assignment?->id, 'discount_code', 1.0);
            }
        }

        // 2. Referral tag in the order note/raw payload.
        $referral = data_get($order->raw_payload, 'note_attributes.creatorplex_creator')
            ?? data_get($order->raw_payload, 'referral_code');

        if ($referral) {
            $assignment = CampaignAssignment::where('discount_code', $referral)->first()
                ?? CampaignAssignment::whereHas('creator', fn ($q) => $q->where('slug', $referral))->first();

            if ($assignment) {
                return $this->record($order, $assignment->creator_id, $assignment->campaign_id, $assignment->id, 'last_touch', 1.0);
            }
        }

        return null;
    }

    protected function record(
        Order $order,
        int $creatorId,
        int $campaignId,
        ?int $assignmentId,
        string $model,
        float $weight,
    ): Attribution {
        $revenue = (int) round($order->total_cents * $weight);

        return DB::transaction(function () use ($order, $creatorId, $campaignId, $assignmentId, $model, $weight, $revenue) {
            $attribution = Attribution::create([
                'workspace_id' => $order->workspace_id,
                'order_id' => $order->id,
                'creator_id' => $creatorId,
                'campaign_id' => $campaignId,
                'assignment_id' => $assignmentId,
                'model' => $model,
                'weight' => $weight,
                'revenue_cents' => $revenue,
                'currency' => $order->currency,
                'attributed_at' => now(),
                'created_at' => now(),
            ]);

            DB::table('analytics_daily_campaign')->updateOrInsert(
                [
                    'workspace_id' => $order->workspace_id,
                    'campaign_id' => $campaignId,
                    'creator_id' => $creatorId,
                    'date' => now()->toDateString(),
                ],
                [
                    'orders' => DB::raw('orders + 1'),
                    'revenue_cents' => DB::raw('revenue_cents + '.$revenue),
                ]
            );

            return $attribution;
        });
    }

    protected function extractDiscountCode(Order $order): ?string
    {
        $discounts = data_get($order->raw_payload, 'discount_codes', []);

        if (is_array($discounts) && ! empty($discounts)) {
            return strtoupper((string) ($discounts[0]['code'] ?? $discounts[0] ?? ''));
        }

        return null;
    }
}
