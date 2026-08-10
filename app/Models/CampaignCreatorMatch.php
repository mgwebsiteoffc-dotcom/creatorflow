<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CampaignCreatorMatch extends Model
{
    protected $fillable = [
        'campaign_id', 'creator_id', 'score', 'reasons',
        'predicted_performance', 'status', 'invited_at', 'responded_at',
    ];

    protected $casts = [
        'reasons' => 'array',
        'predicted_performance' => 'array',
        'score' => 'decimal:2',
        'invited_at' => 'datetime',
        'responded_at' => 'datetime',
    ];

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(Creator::class);
    }
}
