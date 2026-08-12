<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlatformSetting extends Model
{
    protected $fillable = [
        'paid_platform_fee_rate', 'barter_platform_fee_rate',
        'processing_markup_rate', 'processing_markup_fixed_cents',
        'escrow_hold_days', 'minimum_payout_cents',
        'require_creator_verification', 'allow_public_signup',
        'metadata',
        // AI
        'ai_driver', 'ai_openai_key', 'ai_openai_model', 'ai_openai_embedding_model',
        'ai_base_url', 'ai_temperature',
        'ai_last_tested_at', 'ai_last_test_status',
        // Mail (transactional email)
        'mail_driver', 'mail_api_key', 'mail_from_address', 'mail_from_name', 'mail_reply_to',
        'mail_last_tested_at', 'mail_last_test_status', 'mail_enabled', 'inapp_enabled',
        // Analytics + verification scripts (all site-wide)
        'ga4_measurement_id', 'gtm_container_id', 'meta_pixel_id', 'linkedin_partner_id', 'hotjar_id',
        'google_site_verification', 'bing_site_verification',
        'custom_head_html', 'custom_body_html',
        // Razorpay
        'razorpay_key_id', 'razorpay_key_secret', 'razorpay_webhook_secret', 'razorpay_mode',
        'razorpay_last_tested_at', 'razorpay_last_test_status',
        // Feature toggles
        'features_json',
        // PWA push (VAPID)
        'vapid_public_key', 'vapid_private_key', 'vapid_subject',
        // Whatify (WhatsApp)
        'whatify_enabled', 'whatify_api_key', 'whatify_base_url', 'whatify_account_id',
        'whatify_from_number', 'whatify_last_tested_at', 'whatify_last_test_status',
    ];

    protected $casts = [
        'paid_platform_fee_rate'    => 'float',
        'barter_platform_fee_rate'  => 'float',
        'processing_markup_rate'    => 'float',
        'ai_temperature'            => 'float',
        'require_creator_verification' => 'boolean',
        'allow_public_signup'       => 'boolean',
        'metadata'                  => 'array',
        'features_json'             => 'array',
        'ai_last_tested_at'         => 'datetime',
        'mail_last_tested_at'       => 'datetime',
        'razorpay_last_tested_at'   => 'datetime',
        // All secrets stored encrypted at rest
        'ai_openai_key'             => 'encrypted',
        'mail_api_key'              => 'encrypted',
        'razorpay_key_secret'       => 'encrypted',
        'razorpay_webhook_secret'   => 'encrypted',
        'vapid_private_key'         => 'encrypted',
        'whatify_api_key'           => 'encrypted',
        'whatify_enabled'           => 'boolean',
        'whatify_last_tested_at'    => 'datetime',
        'mail_enabled'              => 'boolean',
        'inapp_enabled'             => 'boolean',
    ];

    /**
     * Defaults so preventAccessingMissingAttributes stays quiet when the
     * new AI columns haven't been migrated yet.
     */
    protected $attributes = [
        'ai_driver'                 => 'fake',
        'ai_openai_key'             => null,
        'ai_openai_model'           => 'gpt-4o-mini',
        'ai_openai_embedding_model' => 'text-embedding-3-small',
        'ai_base_url'               => null,
        'ai_temperature'            => 0.40,
        'ai_last_tested_at'         => null,
        'ai_last_test_status'       => null,
        'mail_enabled'              => true,
        'inapp_enabled'             => true,
        'mail_driver'               => 'log',
        'mail_api_key'              => null,
        'mail_from_address'         => null,
        'mail_from_name'            => null,
        'mail_reply_to'             => null,
        'mail_last_tested_at'       => null,
        'mail_last_test_status'     => null,
        'ga4_measurement_id'        => null,
        'gtm_container_id'          => null,
        'meta_pixel_id'             => null,
        'linkedin_partner_id'       => null,
        'hotjar_id'                 => null,
        'google_site_verification'  => null,
        'bing_site_verification'    => null,
        'custom_head_html'          => null,
        'custom_body_html'          => null,
        'razorpay_key_id'           => null,
        'razorpay_key_secret'       => null,
        'razorpay_webhook_secret'   => null,
        'razorpay_mode'             => 'test',
        'razorpay_last_tested_at'   => null,
        'razorpay_last_test_status' => null,
        'features_json'             => null,
        'vapid_public_key'          => null,
        'vapid_private_key'         => null,
        'vapid_subject'             => null,
        'whatify_enabled'           => false,
        'whatify_api_key'           => null,
        'whatify_base_url'          => 'https://app.whatify.in',
        'whatify_account_id'        => null,
        'whatify_from_number'       => null,
        'whatify_last_tested_at'    => null,
        'whatify_last_test_status'  => null,
    ];

    public function maskedWhatifyKey(): string { return $this->mask($this->whatify_api_key); }

    /**
     * Feature flags — everything on the roadmap admin can flip on/off.
     * Defaults are conservative (off) for safety.
     */
    public static function features(): array
    {
        $row = static::current();
        return array_merge([
            'contract_esign'      => false, // #9 e-signature integration
            'referrals'           => false, // #12 refer-a-brand / affiliate program
            'ab_testing'          => false, // #11 landing-page A/B
            'push_notifications'  => false, // #5 PWA push
            'fraud_scan'          => false, // #8 scheduled AI fraud scan
            'auto_content_review' => false, // #13 scheduled AI content review
            'agency_mode'         => false, // #6 agency accounts UI
            'public_creator_pages'=> true,  // #10 /creator/{slug} public portfolio pages
            'case_study_cms'      => true,  // #7 admin-managed case studies
        ], (array) ($row->features_json ?? []));
    }

    public static function feature(string $key): bool
    {
        return (bool) (static::features()[$key] ?? false);
    }

    public function maskedMailKey(): string       { return $this->mask($this->mail_api_key); }
    public function maskedRazorpaySecret(): string { return $this->mask($this->razorpay_key_secret); }
    protected function mask(?string $k): string
    {
        $k = (string) $k;
        if ($k === '') return '';
        if (strlen($k) <= 8) return str_repeat('•', strlen($k));
        return substr($k, 0, 3).str_repeat('•', max(4, strlen($k) - 7)).substr($k, -4);
    }

    /** Get (or create) the single row. */
    public static function current(): self
    {
        return static::firstOrCreate([]);
    }

    /**
     * Never leak the raw key to Blade — mask everything but the last 4 chars.
     */
    public function maskedOpenAiKey(): string
    {
        $k = (string) $this->ai_openai_key;
        if ($k === '') return '';
        if (strlen($k) <= 8) return str_repeat('•', strlen($k));
        return substr($k, 0, 3).str_repeat('•', max(4, strlen($k) - 7)).substr($k, -4);
    }
}
