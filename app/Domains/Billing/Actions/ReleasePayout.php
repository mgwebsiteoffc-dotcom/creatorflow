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

        $rate = (float) config('creatorflow.marketplace.paid_platform_fee_rate');

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
            'scheduled_for' => now()->addDays((int) config('creatorflow.marketplace.payout_clawback_days')),
        ]);

        $assignment->update([
            'payout_id' => $payout->id,
            'status' => 'approved',
        ]);

        // Trigger the Stripe Connect transfer when credentials are configured.
        $this->stripe->transferPayout($payout);

        return $payout;
    }
}
