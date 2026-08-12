<?php

namespace App\Domains\Billing\Actions;

use App\Models\CampaignAssignment;
use App\Models\ContentSubmission;
use App\Models\Payout;
use App\Services\StripeConnectService;

class ReleasePayout
{
    public function __construct(protected StripeConnectService $stripe) {}

    public function handle(ContentSubmission $submission): ?Payout
    {
        $assignment = $submission->assignment;
        $campaign = $assignment->campaign;

        if ($assignment->fee_cents <= 0) {
            // Barter-only assignment — no cash payout.
            $assignment->update(['status' => 'completed']);

            return null;
        }

        if ($assignment->payout_id) {
            return $assignment->payout;
        }

        $rate = (float) config('creatorplex.marketplace.paid_platform_fee_rate');

        // Waive platform fee on growth/enterprise plans.
        if (in_array($campaign->workspace->plan, ['growth', 'enterprise'], true)) {
            $rate = 0.0;
        }

        $platformFee = (int) round($assignment->fee_cents * $rate);
        $processingFee = 0; // Real Stripe fees are tracked on the transfer webhook.
        $net = $assignment->fee_cents - $platformFee - $processingFee;

        $payout = Payout::create([
            'creator_id' => $assignment->creator_id,
            'workspace_id' => $campaign->workspace_id,
            'assignment_id' => $assignment->id,
            'amount_cents' => $assignment->fee_cents,
            'currency' => $campaign->budget_currency,
            'platform_fee_cents' => $platformFee,
            'processing_fee_cents' => $processingFee,
            'net_cents' => $net,
            'method' => 'stripe_connect',
            'status' => 'pending',
            'scheduled_for' => now()->addDays((int) config('creatorplex.marketplace.payout_clawback_days')),
        ]);

        $assignment->update([
            'payout_id' => $payout->id,
            'status' => 'approved',
        ]);

        // Send the actual money — RazorpayX for India, Stripe Connect fallback.
        try {
            $razorpayx = app(\App\Support\RazorpayXService::class);
            if ($razorpayx->isEnabled()) {
                $razorpayx->sendPayout($payout);
            } else {
                $this->stripe->transferPayout($payout);
            }
        } catch (\Throwable $e) {
            report($e);
            $payout->update([
                'status'         => 'failed',
                'failure_reason' => \Illuminate\Support\Str::limit($e->getMessage(), 200),
            ]);
        }

        // Fire creator payout notification (email + WhatsApp).
        \App\Support\NotifyEvent::fire('creator.payout.released', $assignment->creator, [
            'creator_name'   => $assignment->creator->display_name ?? '',
            'brand_name'     => $campaign->workspace->name ?? '',
            'campaign_title' => $campaign->title ?? '',
            'amount'         => number_format($net / 100, 2, '.', ','),
            'link'           => url('/creator/earnings'),
        ]);

        return $payout;
    }
}
