<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CreatorSocialAccount extends Model
{
    protected $fillable = [
        'creator_id', 'platform', 'handle', 'url', 'follower_count',
        'engagement_rate', 'avg_views', 'verified', 'metadata', 'last_synced_at',
    ];

    protected $casts = [
        'metadata' => 'array',
        'verified' => 'boolean',
        'engagement_rate' => 'decimal:2',
        'last_synced_at' => 'datetime',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(Creator::class);
    }
}
