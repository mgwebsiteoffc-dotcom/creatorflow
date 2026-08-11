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
    ];

    protected $casts = [
        'paid_platform_fee_rate'    => 'float',
        'barter_platform_fee_rate'  => 'float',
        'processing_markup_rate'    => 'float',
        'ai_temperature'            => 'float',
        'require_creator_verification' => 'boolean',
        'allow_public_signup'       => 'boolean',
        'metadata'                  => 'array',
        'ai_last_tested_at'         => 'datetime',
        'ai_openai_key'             => 'encrypted', // stored encrypted at rest
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
    ];

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
