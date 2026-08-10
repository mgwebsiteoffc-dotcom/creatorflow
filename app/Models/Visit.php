<?php

namespace App\Models;

use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Visit extends Model
{
    use HasUuid;

    public $timestamps = false;

    protected $fillable = [
        'uuid', 'workspace_id', 'creator_id', 'campaign_id', 'referral_code',
        'utm_source', 'utm_medium', 'utm_campaign', 'landing_url',
        'ip_country', 'referrer', 'created_at',
    ];

    protected $casts = ['created_at' => 'datetime'];

    public function workspace(): BelongsTo
    {
        return $this->belongsTo(Workspace::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(Creator::class);
    }
}
