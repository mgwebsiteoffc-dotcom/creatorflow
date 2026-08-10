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
        'uuid', 'agency_id', 'name', 'website', 'logo_path', 'country',
        'currency', 'timezone', 'plan', 'plan_status', 'onboarding_step',
        'onboarding_completed_at', 'settings',
    ];

    protected $casts = [
        'settings' => 'array',
        'onboarding_completed_at' => 'datetime',
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
}
