<?php

namespace App\Models;

use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Notifications\Notifiable;

class Creator extends Model
{
    use HasUuid, Notifiable;

    protected $fillable = [
        'uuid', 'user_id', 'display_name', 'slug', 'avatar_path', 'bio',
        'email', 'phone', 'country', 'city', 'state', 'languages', 'niches', 'status',
        'gender', 'age_range', 'tier',
        'open_to_work', 'accepts_barter', 'accepts_paid', 'accepts_affiliate',
        'rate_ugc_cents', 'rate_post_cents', 'rate_video_cents', 'rate_story_cents',
        'currency', 'follower_count_total', 'engagement_rate', 'avg_views',
        'audience_female_pct', 'audience_male_pct', 'audience_top_age',
        'performance_score', 'fraud_risk', 'stripe_connect_id',
        'payout_method_status', 'ai_summary', 'metadata',
    ];

    protected $casts = [
        'languages' => 'array',
        'niches' => 'array',
        'metadata' => 'array',
        'open_to_work' => 'boolean',
        'accepts_barter' => 'boolean',
        'accepts_paid' => 'boolean',
        'accepts_affiliate' => 'boolean',
        'engagement_rate' => 'decimal:2',
        'performance_score' => 'decimal:2',
        'fraud_risk' => 'decimal:2',
        'rate_ugc_cents' => 'integer',
        'rate_post_cents' => 'integer',
        'rate_video_cents' => 'integer',
        'rate_story_cents' => 'integer',
        'suspended_at' => 'datetime',
    ];

    protected $attributes = [
        'suspension_reason'    => null,
        'suspended_at'         => null,
        'state'                => null,
        'tier'                 => null,
        'gender'               => null,
        'age_range'            => null,
        'audience_female_pct'  => null,
        'audience_male_pct'    => null,
        'audience_top_age'     => null,
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function socialAccounts(): HasMany
    {
        return $this->hasMany(CreatorSocialAccount::class);
    }

    public function portfolioItems(): HasMany
    {
        return $this->hasMany(CreatorPortfolioItem::class)->orderBy('position');
    }

    public function preferences(): HasOne
    {
        return $this->hasOne(CreatorPreference::class);
    }

    public function nicheRows(): HasMany
    {
        return $this->hasMany(CreatorNiche::class);
    }

    public function audienceSnapshots(): HasMany
    {
        return $this->hasMany(CreatorAudienceSnapshot::class);
    }

    public function matches(): HasMany
    {
        return $this->hasMany(CampaignCreatorMatch::class);
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(CampaignAssignment::class);
    }

    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }

    public function invitations(): HasMany
    {
        return $this->hasMany(CampaignInvitation::class);
    }

    public function payouts(): HasMany
    {
        return $this->hasMany(Payout::class);
    }

    public function notifications()
    {
        return $this->hasMany(AppNotification::class, 'recipient_id')
            ->where('recipient_type', 'creator')
            ->latest();
    }

    public function isSuspended(): bool
    {
        return $this->status === 'suspended';
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active')->where('open_to_work', true);
    }

    public function acceptsCampaignType(string $type): bool
    {
        return match ($type) {
            'barter' => $this->accepts_barter,
            'paid', 'hybrid' => $this->accepts_paid,
            'affiliate' => $this->accepts_affiliate,
            default => false,
        };
    }

    /**
     * Derive the creator's tier from follower count if not stored.
     */
    public function currentTier(): ?string
    {
        return $this->tier ?: \App\Support\CreatorTaxonomy::tierFromFollowers((int) $this->follower_count_total);
    }

    /**
     * Filter by cities (slugs OR raw city names — case-insensitive).
     */
    public function scopeInCities($query, array $cities)
    {
        $cities = array_filter(array_map('strval', $cities));
        if (empty($cities)) return $query;

        $names = collect($cities)->map(function ($c) {
            $c = strtolower(trim($c));
            // Convert slug (e.g. "new-delhi") back to a name for compare.
            $known = \App\Support\CreatorTaxonomy::cities()[$c] ?? null;
            return $known ? strtolower($known['name']) : str_replace('-', ' ', $c);
        })->all();

        return $query->where(function ($q) use ($names) {
            foreach ($names as $n) {
                $q->orWhereRaw('LOWER(city) = ?', [$n]);
            }
        });
    }

    /**
     * Filter by tier slugs (nano, micro, mid, macro, mega).
     * Uses stored tier column if present, else falls back to follower count ranges.
     */
    public function scopeInTiers($query, array $tiers)
    {
        $tiers = array_values(array_intersect($tiers, array_keys(\App\Support\CreatorTaxonomy::tiers())));
        if (empty($tiers)) return $query;

        $ranges = \App\Support\CreatorTaxonomy::tiers();

        $hasTierCol = \Illuminate\Support\Facades\Schema::hasColumn('creators', 'tier');

        return $query->where(function ($q) use ($tiers, $ranges, $hasTierCol) {
            foreach ($tiers as $t) {
                $q->orWhere(function ($qq) use ($t, $ranges, $hasTierCol) {
                    $r = $ranges[$t];
                    if ($hasTierCol) {
                        $qq->where(function ($x) use ($t, $r) {
                            $x->where('tier', $t)
                              ->orWhere(function ($y) use ($r) {
                                  $y->whereNull('tier')
                                    ->where('follower_count_total', '>=', $r['min']);
                                  if ($r['max']) $y->where('follower_count_total', '<', $r['max']);
                              });
                        });
                    } else {
                        $qq->where('follower_count_total', '>=', $r['min']);
                        if ($r['max']) $qq->where('follower_count_total', '<', $r['max']);
                    }
                });
            }
        });
    }

    public function scopeGenderIn($query, array $genders)
    {
        $genders = array_values(array_intersect($genders, array_keys(\App\Support\CreatorTaxonomy::genders())));
        if (empty($genders)) return $query;
        if (! \Illuminate\Support\Facades\Schema::hasColumn('creators', 'gender')) return $query;
        return $query->whereIn('gender', $genders);
    }

    public function scopeAgeIn($query, array $ranges)
    {
        $ranges = array_values(array_intersect($ranges, array_keys(\App\Support\CreatorTaxonomy::ageRanges())));
        if (empty($ranges)) return $query;
        if (! \Illuminate\Support\Facades\Schema::hasColumn('creators', 'age_range')) return $query;
        return $query->whereIn('age_range', $ranges);
    }

    public function scopeLanguageIn($query, array $codes)
    {
        $codes = array_values(array_intersect($codes, array_keys(\App\Support\CreatorTaxonomy::languages())));
        if (empty($codes)) return $query;

        return $query->where(function ($q) use ($codes) {
            foreach ($codes as $c) {
                // languages is JSON array of ISO codes
                $q->orWhereJsonContains('languages', $c);
            }
        });
    }
}
