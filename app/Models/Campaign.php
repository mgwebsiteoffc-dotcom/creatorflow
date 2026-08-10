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
        'deliverables', 'usage_rights', 'exclusivity', 'start_date', 'end_date',
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
