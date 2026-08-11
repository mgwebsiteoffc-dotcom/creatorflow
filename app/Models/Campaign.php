<?php

namespace App\Models;

use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Campaign extends Model
{
    use HasUuid;

    protected $fillable = [
        'uuid', 'workspace_id', 'created_by', 'title', 'type', 'status',
        'niche', 'summary', 'brief', 'objectives', 'content_types',
        'deliverables', 'usage_rights', 'exclusivity', 'audience_criteria',
        'start_date', 'end_date',
        'budget_total_cents', 'budget_currency', 'creator_fee_cents',
        'product_cost_cents', 'commission_rate', 'target_creators',
        'invite_pool_size', 'acceptance_rate_assumed', 'waitlist_size',
        'ai_generated', 'ai_predicted_roi', 'ai_metadata', 'launched_at',
        'completed_at',
    ];

    protected $casts = [
        'objectives' => 'array',
        'content_types' => 'array',
        'deliverables' => 'array',
        'usage_rights' => 'array',
        'exclusivity' => 'array',
        'audience_criteria' => 'array',
        'ai_metadata' => 'array',
        'start_date' => 'date',
        'end_date' => 'date',
        'launched_at' => 'datetime',
        'completed_at' => 'datetime',
        'ai_generated' => 'boolean',
        'commission_rate' => 'decimal:2',
        'acceptance_rate_assumed' => 'decimal:2',
        'ai_predicted_roi' => 'decimal:2',
    ];

    protected $attributes = [
        'audience_criteria' => null,
    ];

    /**
     * Convenience helper — describe audience picks in plain English for chips/summaries.
     */
    public function audienceSummary(): array
    {
        $a = (array) ($this->audience_criteria ?? []);
        $tax = \App\Support\CreatorTaxonomy::class;
        $out = [];

        if (! empty($a['cities'])) {
            $names = collect($a['cities'])->map(fn ($c) => $tax::cities()[$c]['name'] ?? ucwords(str_replace('-', ' ', $c)))->all();
            $out[] = 'Cities: '.implode(', ', $names);
        }
        if (! empty($a['tiers'])) {
            $names = collect($a['tiers'])->map(fn ($t) => $tax::tiers()[$t]['label'] ?? $t)->all();
            $out[] = 'Tiers: '.implode(', ', $names);
        }
        if (! empty($a['genders'])) {
            $names = collect($a['genders'])->map(fn ($g) => $tax::genders()[$g] ?? $g)->all();
            $out[] = 'Creator gender: '.implode(', ', $names);
        }
        if (! empty($a['age_ranges'])) {
            $out[] = 'Creator age: '.implode(', ', $a['age_ranges']);
        }
        if (! empty($a['languages'])) {
            $names = collect($a['languages'])->map(fn ($l) => $tax::languages()[$l] ?? $l)->all();
            $out[] = 'Languages: '.implode(', ', $names);
        }
        if (! empty($a['audience_gender']) && ! empty($a['audience_min_pct'])) {
            $g = $tax::genders()[$a['audience_gender']] ?? $a['audience_gender'];
            $out[] = "Audience {$g} ≥ {$a['audience_min_pct']}%";
        }
        if (! empty($a['min_followers']) || ! empty($a['max_followers'])) {
            $out[] = 'Followers: '.($a['min_followers'] ? number_format($a['min_followers']) : '0').' – '.($a['max_followers'] ? number_format($a['max_followers']) : '∞');
        }
        if (! empty($a['min_engagement'])) {
            $out[] = 'ER ≥ '.$a['min_engagement'].'%';
        }
        return $out;
    }

    public function workspace(): BelongsTo
    {
        return $this->belongsTo(Workspace::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function products(): HasMany
    {
        return $this->hasMany(CampaignProduct::class);
    }

    public function matches(): HasMany
    {
        return $this->hasMany(CampaignCreatorMatch::class);
    }

    public function invitations(): HasMany
    {
        return $this->hasMany(CampaignInvitation::class);
    }

    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(CampaignAssignment::class);
    }

    public function waitlist(): HasMany
    {
        return $this->hasMany(WaitlistEntry::class);
    }

    public function discountCodes(): HasMany
    {
        return $this->hasMany(DiscountCode::class);
    }

    public function references(): HasMany
    {
        return $this->hasMany(CampaignReference::class)->latest();
    }

    public function scopeActive($query)
    {
        return $query->whereIn('status', ['matching', 'inviting', 'active']);
    }

    public function isLaunched(): bool
    {
        return in_array($this->status, ['inviting', 'active', 'paused', 'completed'], true);
    }

    public function totalTargetCreators(): int
    {
        return (int) $this->products()->sum('target_creators') ?: $this->target_creators;
    }
}
