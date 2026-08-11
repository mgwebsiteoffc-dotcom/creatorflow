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
    ];

    protected $casts = [
        'paid_platform_fee_rate'    => 'float',
        'barter_platform_fee_rate'  => 'float',
        'processing_markup_rate'    => 'float',
        'require_creator_verification' => 'boolean',
        'allow_public_signup'       => 'boolean',
        'metadata'                  => 'array',
    ];

    /** Get (or create) the single row. */
    public static function current(): self
    {
        return static::firstOrCreate([]);
    }
}
