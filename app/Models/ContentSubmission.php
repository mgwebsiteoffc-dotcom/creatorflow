<?php

namespace App\Models;

use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ContentSubmission extends Model
{
    use HasUuid;

    protected $fillable = [
        'uuid', 'assignment_id', 'creator_id', 'campaign_id', 'type', 'disk',
        'path', 'thumbnail_path', 'caption', 'external_post_url', 'metadata',
        'ai_score', 'ai_feedback', 'ai_flags', 'status', 'submitted_at',
        'reviewed_at', 'approved_at',
    ];

    protected $casts = [
        'metadata' => 'array',
        'ai_feedback' => 'array',
        'ai_flags' => 'array',
        'ai_score' => 'decimal:2',
        'submitted_at' => 'datetime',
        'reviewed_at' => 'datetime',
        'approved_at' => 'datetime',
    ];

    public function assignment(): BelongsTo
    {
        return $this->belongsTo(CampaignAssignment::class, 'assignment_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(Creator::class);
    }

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(ContentReview::class);
    }

    public function approve(): void
    {
        $this->update(['status' => 'approved', 'approved_at' => now(), 'reviewed_at' => now()]);
    }

    public function requestChanges(?string $comment = null): void
    {
        $this->update(['status' => 'changes_requested', 'reviewed_at' => now()]);
        if ($comment) {
            $this->reviews()->create([
                'reviewer_type' => 'brand',
                'decision' => 'changes_requested',
                'comment' => $comment,
                'created_at' => now(),
            ]);
        }
    }
}
