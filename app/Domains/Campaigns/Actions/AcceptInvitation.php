<?php

namespace App\Domains\Campaigns\Actions;

use App\Domains\Commerce\Channels\ChannelRegistry;
use App\Domains\Contracts\GenerateContract;
use App\Events\CreatorAcceptedInvitation;
use App\Jobs\CreateCreatorOrder;
use App\Models\Campaign;
use App\Models\CampaignAssignment;
use App\Models\CampaignCreatorMatch;
use App\Models\CampaignInvitation;
use App\Models\CampaignProduct;
use App\Models\Creator;
use Illuminate\Support\Facades\DB;

/**
 * Orchestrates everything that happens when a creator accepts an invitation:
 *   - resolves the campaign product slot
 *   - creates the assignment record
 *   - generates and sends the contract
 *   - creates a unique discount code
 *   - dispatches order creation (Shopify draft/$0 order or manual order)
 *   - promotes the next waitlisted creator if needed
 */
class AcceptInvitation
{
    public function __construct(
        protected ChannelRegistry $channels,
        protected GenerateContract $generateContract,
    ) {}

    public function handle(CampaignInvitation $invitation): CampaignAssignment
    {
        return DB::transaction(function () use ($invitation) {
            $invitation->update([
                'status' => 'accepted',
                'responded_at' => now(),
            ]);

            $campaign = $invitation->campaign;
            $creator = $invitation->creator;

            CampaignCreatorMatch::where('campaign_id', $campaign->id)
                ->where('creator_id', $creator->id)
                ->update(['status' => 'accepted', 'responded_at' => now()]);

            $campaignProduct = $this->resolveCampaignProduct($campaign, $invitation);

            $assignment = CampaignAssignment::create([
                'campaign_id' => $campaign->id,
                'campaign_product_id' => $campaignProduct->id,
                'creator_id' => $creator->id,
                'status' => 'accepted',
                'fee_cents' => $campaignProduct->fee_cents ?: $campaign->creator_fee_cents,
                'commission_rate' => $campaignProduct->commission_rate ?? $campaign->commission_rate,
                'content_due_date' => $campaign->end_date,
            ]);

            // Contract
            $contract = $this->generateContract->handle($assignment);
            $assignment->update(['contract_id' => $contract->id, 'status' => 'contract_sent']);

            // Discount code through the active channel (Shopify or manual).
            $channel = $this->channels->forWorkspace($campaign->workspace);
            $discount = $channel->createDiscountCode(
                $campaign->workspace,
                $campaignProduct->product,
                $creator,
                [
                    'campaign_id' => $campaign->id,
                    'type' => $campaign->type === 'affiliate' ? 'percentage' : 'full_comp',
                    'value' => $campaign->type === 'affiliate' ? 15 : 100,
                ]
            );
            $assignment->update(['discount_code' => $discount->code]);

            $campaignProduct->increment('accepted_count');

            // Order creation runs as a job so Shopify API latency never blocks.
            CreateCreatorOrder::dispatch($assignment->id);

            event(new CreatorAcceptedInvitation($invitation->id));

            // Notify every brand user (email + WhatsApp + in-app via template).
            foreach ($campaign->workspace?->users ?? [] as $brandUser) {
                \App\Support\NotifyEvent::fire('brand.creator.accepted', $brandUser, [
                    'brand_name'     => $campaign->workspace->name,
                    'creator_name'   => $creator->display_name,
                    'campaign_title' => $campaign->title,
                    'link'           => route('brand.assignments.show', $assignment),
                ]);
            }

            return $assignment->fresh();
        });
    }

    protected function resolveCampaignProduct(Campaign $campaign, CampaignInvitation $invitation): CampaignProduct
    {
        if ($invitation->campaign_product_id) {
            return $campaign->products()->findOrFail($invitation->campaign_product_id);
        }

        // Allocate to the product with the most open slots.
        return $campaign->products
            ->sortByDesc(fn (CampaignProduct $cp) => $cp->openSlots())
            ->first();
    }
}
