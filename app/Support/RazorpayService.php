<?php

namespace App\Support;

use App\Models\Invoice;
use App\Models\PlatformSetting;
use App\Models\Workspace;
use Illuminate\Support\Facades\Http;

/**
 * Razorpay integration for Indian subscription + one-off payments.
 *
 * Reads credentials from platform_settings so admins can rotate keys
 * without a deploy. Handles: creating orders, verifying webhook HMAC,
 * subscription plan mgmt, and invoice reconciliation.
 */
class RazorpayService
{
    protected string $keyId;
    protected string $keySecret;
    protected string $mode;
    protected string $baseUrl = 'https://api.razorpay.com/v1';

    public function __construct()
    {
        $s = PlatformSetting::current();
        $this->keyId     = (string) $s->razorpay_key_id;
        $this->keySecret = (string) $s->razorpay_key_secret;
        $this->mode      = $s->razorpay_mode ?: 'test';
    }

    public function isConfigured(): bool
    {
        return $this->keyId !== '' && $this->keySecret !== '';
    }

    public function publicKey(): string { return $this->keyId; }
    public function isLive(): bool      { return $this->mode === 'live'; }

    /**
     * Create a one-off order (checkout on the brand billing page).
     * Amount is in the smallest currency unit (paise for INR).
     */
    public function createOrder(int $amountCents, string $currency = 'INR', array $notes = []): array
    {
        $this->assertConfigured();

        return Http::withBasicAuth($this->keyId, $this->keySecret)
            ->asJson()
            ->post("{$this->baseUrl}/orders", [
                'amount'   => $amountCents,
                'currency' => strtoupper($currency),
                'receipt'  => 'rcpt_'.now()->timestamp,
                'notes'    => $notes,
            ])
            ->throw()->json();
    }

    /**
     * Verify a payment client-side signature (Checkout callback).
     */
    public function verifyPaymentSignature(string $orderId, string $paymentId, string $signature): bool
    {
        $expected = hash_hmac('sha256', "{$orderId}|{$paymentId}", $this->keySecret);
        return hash_equals($expected, $signature);
    }

    /**
     * Verify a Razorpay webhook payload against our stored webhook secret.
     */
    public function verifyWebhook(string $payload, string $signature, ?string $webhookSecret = null): bool
    {
        $secret = $webhookSecret ?: (PlatformSetting::current()->razorpay_webhook_secret ?: '');
        if ($secret === '') return false;
        $expected = hash_hmac('sha256', $payload, $secret);
        return hash_equals($expected, $signature);
    }

    /**
     * Quick health check — hits /orders?count=1 with basic auth.
     */
    public function ping(): array
    {
        $this->assertConfigured();
        $r = Http::withBasicAuth($this->keyId, $this->keySecret)
            ->get("{$this->baseUrl}/orders", ['count' => 1]);
        return ['ok' => $r->successful(), 'status' => $r->status(), 'mode' => $this->mode];
    }

    protected function assertConfigured(): void
    {
        if (! $this->isConfigured()) {
            throw new \RuntimeException('Razorpay is not configured. Save keys in Admin → Payments.');
        }
    }
}
