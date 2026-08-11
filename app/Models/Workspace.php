<?php

namespace App\Models;

use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Workspace extends Model
{
    use HasUuid;

    protected $fillable = [
        'uuid', 'agency_id', 'name', 'legal_name', 'website',
        'contact_email', 'contact_phone',
        'logo_path', 'country', 'currency', 'timezone',
        'address_line1', 'address_line2', 'address_city',
        'address_state', 'address_postal',
        'tax_type', 'tax_id', 'billing_notes',
        'plan', 'plan_status', 'onboarding_step',
        'onboarding_completed_at', 'settings',
    ];

    protected $casts = [
        'settings' => 'array',
        'onboarding_completed_at' => 'datetime',
        'suspended_at' => 'datetime',
    ];

    /**
     * Default values so accessing a column that hasn't been migrated yet
     * doesn't blow up in Laravel 11+ strict-mode. Also prevents
     * MissingAttributeException when a query selects a partial set.
     */
    protected $attributes = [
        'legal_name'         => null,
        'contact_email'      => null,
        'contact_phone'      => null,
        'address_line1'      => null,
        'address_line2'      => null,
        'address_city'       => null,
        'address_state'      => null,
        'address_postal'     => null,
        'tax_type'           => null,
        'tax_id'             => null,
        'billing_notes'      => null,
        'account_status'     => 'active',
        'suspension_reason'  => null,
        'suspended_at'       => null,
    ];

    public function agency(): BelongsTo
    {
        return $this->belongsTo(Agency::class);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'workspace_users')
            ->withPivot('role', 'invited_at', 'accepted_at')
            ->withTimestamps();
    }

    public function channels(): HasMany
    {
        return $this->hasMany(Channel::class);
    }

    public function shopifyChannel(): HasOne
    {
        return $this->hasOne(Channel::class)->where('type', 'shopify');
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function campaigns(): HasMany
    {
        return $this->hasMany(Campaign::class);
    }

    public function subscription(): HasOne
    {
        return $this->hasOne(Subscription::class)->latestOfMany();
    }

    public function entitlements(): array
    {
        return config("creatorflow.plans.{$this->plan}", config('creatorflow.plans.free'));
    }

    public function onboardingComplete(): bool
    {
        return $this->onboarding_completed_at !== null;
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function paymentRecords(): HasMany
    {
        return $this->hasMany(PaymentRecord::class);
    }

    public function currencySymbol(): string
    {
        return static::symbolFor($this->currency ?: 'USD');
    }

    public function formatMoney(int $cents, ?string $currency = null): string
    {
        $currency = strtoupper($currency ?: ($this->currency ?: 'USD'));
        $amount = $cents / 100;
        $formatted = $currency === 'INR'
            ? number_format($amount, 2, '.', ',')
            : number_format($amount, 2);

        return static::symbolFor($currency).$formatted;
    }

    public static function symbolFor(string $code): string
    {
        return match (strtoupper($code)) {
            'INR' => '₹',
            'USD', 'CAD', 'AUD', 'SGD', 'NZD' => '$',
            'EUR' => '€',
            'GBP' => '£',
            'AED' => 'د.إ ',
            'JPY' => '¥',
            default => $code.' ',
        };
    }

    public static function supportedCurrencies(): array
    {
        return [
            'INR' => 'INR · ₹ Indian Rupee',
            'USD' => 'USD · $ US Dollar',
            'EUR' => 'EUR · € Euro',
            'GBP' => 'GBP · £ British Pound',
            'AED' => 'AED · UAE Dirham',
            'AUD' => 'AUD · $ Australian Dollar',
            'CAD' => 'CAD · $ Canadian Dollar',
            'SGD' => 'SGD · $ Singapore Dollar',
            'JPY' => 'JPY · ¥ Japanese Yen',
        ];
    }
}
