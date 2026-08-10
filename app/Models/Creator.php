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
        'email', 'phone', 'country', 'city', 'languages', 'niches', 'status',
        'open_to_work', 'accepts_barter', 'accepts_paid', 'accepts_affiliate',
        'rate_ugc_cents', 'rate_post_cents', 'rate_video_cents', 'rate_story_cents',
        'currency', 'follower_count_total', 'engagement_rate', 'avg_views',
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

    public function payouts(): HasMany
    {
        return $this->hasMany(Payout::class);
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
}
