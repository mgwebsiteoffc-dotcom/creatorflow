<?php

namespace App\Support;

use App\Models\Creator;
use App\Models\Payout;
use App\Models\PlatformSetting;
use Illuminate\Support\Facades\Http;

/**
 * RazorpayX = the payouts side of the same Razorpay account. Same key_id +
 * key_secret as regular Razorpay (basic auth), separate account_number for
 * the virtual account that money comes out of.
 *
 * Docs: https://razorpay.com/docs/x/apis/
 */
class RazorpayXService
{
    protected string $baseUrl = 'https://api.razorpay.com/v1';
    protected string $keyId;
    protected string $keySecret;
    protected string $accountNumber;
    protected string $mode;

    public function __construct()
    {
        $s = PlatformSetting::current();
        $this->keyId         = (string) $s->razorpay_key_id;
        $this->keySecret     = (string) $s->razorpay_key_secret;
        $this->accountNumber = (string) $s->razorpayx_account_number;
        $this->mode          = strtoupper($s->razorpayx_mode ?: 'IMPS');
    }

    public function isEnabled(): bool
    {
        $s = PlatformSetting::current();
        return (bool) $s->razorpayx_enabled
            && $this->keyId !== '' && $this->keySecret !== ''
            && $this->accountNumber !== '';
    }

    /**
     * Ensure the creator has a RazorpayX Contact + Fund Account. Cached on the
     * creator row so we only create them once.
     */
    public function ensureCreatorProvisioned(Creator $creator): void
    {
        if (! $this->isEnabled()) return;

        // Contact
        if (empty($creator->razorpayx_contact_id)) {
            $r = Http::withBasicAuth($this->keyId, $this->keySecret)
                ->asJson()
                ->post("{$this->baseUrl}/contacts", [
                    'name'    => $creator->display_name,
                    'email'   => $creator->email,
                    'contact' => $creator->phone,
                    'type'    => 'vendor',
                    'reference_id' => 'creator_'.$creator->id,
                ])->throw()->json();
            $creator->razorpayx_contact_id = $r['id'] ?? null;
        }

        // Fund account (UPI or bank)
        if (empty($creator->razorpayx_fund_account_id) && $creator->razorpayx_contact_id) {
            $payload = ['contact_id' => $creator->razorpayx_contact_id];
            if ($creator->payout_method === 'upi' && $creator->upi_vpa) {
                $payload += ['account_type' => 'vpa', 'vpa' => ['address' => $creator->upi_vpa]];
            } elseif ($creator->payout_method === 'bank' && $creator->bank_account_number && $creator->bank_ifsc) {
                $payload += [
                    'account_type'  => 'bank_account',
                    'bank_account'  => [
                        'name'           => $creator->bank_account_holder_name ?: $creator->display_name,
                        'ifsc'           => strtoupper($creator->bank_ifsc),
                        'account_number' => $creator->bank_account_number,
                    ],
                ];
            } else {
                return; // Not enough info yet; retry on next attempt.
            }

            $r = Http::withBasicAuth($this->keyId, $this->keySecret)
                ->asJson()
                ->post("{$this->baseUrl}/fund_accounts", $payload)
                ->throw()->json();
            $creator->razorpayx_fund_account_id = $r['id'] ?? null;
        }

        $creator->save();
    }

    /**
     * Fire an actual payout to the creator's fund account. Returns the raw
     * RazorpayX payout row so caller can update local Payout with the id.
     *
     * @throws \RuntimeException on any 4xx/5xx or missing pre-req
     */
    public function sendPayout(Payout $payout): array
    {
        if (! $this->isEnabled()) {
            throw new \RuntimeException('RazorpayX not enabled — save keys in Admin → Integrations.');
        }

        $creator = $payout->creator;
        if (! $creator) throw new \RuntimeException('Payout has no creator');

        $this->ensureCreatorProvisioned($creator);
        if (empty($creator->razorpayx_fund_account_id)) {
            throw new \RuntimeException('Creator has no UPI VPA or bank account set — cannot pay out.');
        }

        $mode = strtoupper($payout->method ?: $this->mode);
        // RazorpayX accepts IMPS, NEFT, RTGS, UPI, card
        if (! in_array($mode, ['IMPS','NEFT','RTGS','UPI','card'], true)) $mode = 'IMPS';
        // UPI payouts only work with a VPA fund account.
        if ($creator->payout_method === 'upi') $mode = 'UPI';

        $r = Http::withBasicAuth($this->keyId, $this->keySecret)
            ->withHeaders(['X-Payout-Idempotency' => 'payout_'.$payout->id.'_v1'])
            ->asJson()
            ->post("{$this->baseUrl}/payouts", [
                'account_number' => $this->accountNumber,
                'fund_account_id'=> $creator->razorpayx_fund_account_id,
                'amount'         => (int) $payout->net_cents,           // in paise
                'currency'       => 'INR',
                'mode'           => $mode,
                'purpose'        => 'payout',                            // custom purpose 'creator_payout' needs pre-approval
                'queue_if_low_balance' => true,
                'reference_id'   => 'payout_'.$payout->id,
                'narration'      => 'CreatorPlex payout',
            ])
            ->throw()->json();

        $payout->update([
            'provider'        => 'razorpayx',
            'external_id'     => $r['id'] ?? null,
            'external_status' => $r['status'] ?? 'queued',
            'processed_at'    => now(),
            'status'          => match ($r['status'] ?? '') {
                'processed', 'processing' => 'paid',
                'reversed', 'rejected', 'failed', 'cancelled' => 'failed',
                default => 'pending',
            },
        ]);

        return $r;
    }

    public function ping(): array
    {
        if (! $this->isEnabled()) return ['ok' => false, 'error' => 'RazorpayX not enabled'];
        try {
            $r = Http::withBasicAuth($this->keyId, $this->keySecret)
                ->timeout(10)
                ->get("{$this->baseUrl}/contacts", ['count' => 1]);
            return ['ok' => $r->successful(), 'status' => $r->status()];
        } catch (\Throwable $e) {
            return ['ok' => false, 'error' => $e->getMessage()];
        }
    }
}
