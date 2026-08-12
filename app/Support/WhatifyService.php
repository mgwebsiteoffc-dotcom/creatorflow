<?php

namespace App\Support;

use App\Models\PlatformSetting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Whatify WhatsApp Business API client.
 *
 * Docs: https://whatify.docs.buildwithfern.com/whatify-external-api-v-1/introduction
 * All requests carry an X-API-Key header.
 */
class WhatifyService
{
    protected string $baseUrl;
    protected ?string $apiKey;
    protected ?string $accountId;

    public function __construct()
    {
        $s = PlatformSetting::current();
        $this->baseUrl   = rtrim($s->whatify_base_url ?: 'https://app.whatify.in', '/');
        $this->apiKey    = $s->whatify_api_key ?: null;
        $this->accountId = $s->whatify_account_id ?: null;
    }

    public function isEnabled(): bool
    {
        $s = PlatformSetting::current();
        return (bool) $s->whatify_enabled && $this->apiKey && $this->baseUrl;
    }

    /**
     * Send a free-form text message. Only works in the 24h customer-service
     * window — otherwise Whatsapp will reject it. Use a template outside that window.
     */
    public function sendMessage(string $phone, string $message): array
    {
        if (! $this->isEnabled()) return ['ok' => false, 'error' => 'Whatify disabled'];
        try {
            $r = Http::withHeaders(['X-API-Key' => $this->apiKey])
                ->asJson()
                ->timeout(20)
                ->post($this->baseUrl.'/api/v1/external/send-message', array_filter([
                    'phone'   => $this->normalizePhone($phone),
                    'message' => $message,
                    'whatsapp_account_id' => $this->accountId,
                ]))->throw();
            return ['ok' => true, 'body' => $r->json()];
        } catch (\Throwable $e) {
            report($e);
            return ['ok' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Send an approved WhatsApp template. body_params are the {{1}} {{2}} …
     * variables in your template body; header_params are variables in the header.
     */
    public function sendTemplate(string $phone, string $templateName, array $bodyParams = [], array $headerParams = []): array
    {
        if (! $this->isEnabled()) return ['ok' => false, 'error' => 'Whatify disabled'];
        try {
            $r = Http::withHeaders(['X-API-Key' => $this->apiKey])
                ->asJson()
                ->timeout(20)
                ->post($this->baseUrl.'/api/v1/external/send-template', array_filter([
                    'phone'         => $this->normalizePhone($phone),
                    'template_name' => $templateName,
                    'body_params'   => array_values(array_map('strval', $bodyParams)),
                    'header_params' => array_values($headerParams),
                    'whatsapp_account_id' => $this->accountId,
                ], fn ($v) => $v !== null))->throw();
            return ['ok' => true, 'body' => $r->json()];
        } catch (\Throwable $e) {
            report($e);
            return ['ok' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Quick health check — GET /health/ping.
     */
    public function ping(): array
    {
        if (empty($this->apiKey)) return ['ok' => false, 'error' => 'No API key set'];
        try {
            $r = Http::withHeaders(['X-API-Key' => $this->apiKey])
                ->timeout(10)
                ->get($this->baseUrl.'/api/v1/external/health/ping');
            return ['ok' => $r->successful(), 'status' => $r->status(), 'body' => $r->json()];
        } catch (\Throwable $e) {
            return ['ok' => false, 'error' => $e->getMessage()];
        }
    }

    public function walletBalance(): array
    {
        if (! $this->isEnabled()) return ['ok' => false, 'error' => 'Whatify disabled'];
        try {
            $r = Http::withHeaders(['X-API-Key' => $this->apiKey])
                ->timeout(10)
                ->get($this->baseUrl.'/api/v1/external/wallet/balance');
            return ['ok' => $r->successful(), 'body' => $r->json()];
        } catch (\Throwable $e) {
            return ['ok' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Normalise to E.164 without +. Whatify expects a plain digit string like
     * "919876543210". Strips + and any non-digits.
     */
    protected function normalizePhone(string $phone): string
    {
        $phone = preg_replace('/\D+/', '', $phone) ?? '';
        // If it's a 10-digit Indian number, prepend 91.
        if (strlen($phone) === 10 && str_starts_with($phone, '9') === false && $phone[0] > '5') {
            $phone = '91'.$phone;
        } elseif (strlen($phone) === 10) {
            $phone = '91'.$phone;
        }
        return $phone;
    }
}
