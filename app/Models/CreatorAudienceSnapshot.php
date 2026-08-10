<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CreatorAudienceSnapshot extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'creator_id', 'social_account_id', 'snapshot_date', 'followers',
        'engagement', 'avg_views', 'age_buckets', 'gender_split',
        'top_countries', 'top_cities', 'fake_follower_pct', 'created_at',
    ];

    protected $casts = [
        'age_buckets' => 'array',
        'gender_split' => 'array',
        'top_countries' => 'array',
        'top_cities' => 'array',
        'snapshot_date' => 'date',
        'created_at' => 'datetime',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(Creator::class);
    }
}
