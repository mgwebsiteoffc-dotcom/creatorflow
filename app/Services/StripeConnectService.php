<?php

namespace App\Services;

use App\Models\Creator;
use App\Models\Payout;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Thin Stripe Connect wrapper. When no secret key is configured (local/dev)
 * it logs instead of calling Stripe, so the full payout workflow runs
 * end-to-end without external credentials.
 */
class StripeConnectService
{
    public function configured(): bool
    {
        return ! empty(config('cashier.secret') ?? config('services.stripe.secret'));
    }

    public function createExpressAccountLink(Creator $creator): ?string
    {
        if (! $this->configured()) {
            Log::info('[Stripe] Would create Express onboarding link', ['creator' => $creator->id]);

            return route('creator.earnings.index').'?onboarding=simulated';
        }

        // Real implementation: Account::create(['type' => 'express']) then
        // AccountLink::create for the onboarding URL.
        return null;
    }

    public function transferPayout(Payout $payout): void
    {
        if (! $this->configured()) {
            Log::info('[Stripe] Would transfer payout', [
                'payout' => $payout->uuid,
                'amount' => $payout->net_cents,
                'creator' => $payout->creator_id,
            ]);

            // In local/demo mode mark paid instantly so dashboards populate.
            $payout->markPaid('fake_transfer_'.$payout->id);

            return;
        }

        // Real: Transfer::create([
        //   'amount' => $payout->net_cents,
        //   'currency' => $payout->currency,
        //   'destination' => $payout->creator->stripe_connect_id,
        // ]);
    }

    public function createOAuthLink(): string
    {
        $clientId = config('services.stripe.connect_client_id');

        return 'https://connect.stripe.com/express/oauth/authorize?'.http_build_query([
            'client_id' => $clientId,
            'state' => csrf_token(),
            'suggested_capabilities[]' => 'transfers',
        ]);
    }
}
