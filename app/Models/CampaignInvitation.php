<?php

namespace App\Models;

use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CampaignInvitation extends Model
{
    use HasUuid;

    protected $fillable = [
        'uuid', 'campaign_id', 'creator_id', 'campaign_product_id',
        'channel', 'message', 'ai_variant', 'status', 'sent_at',
        'opened_at', 'responded_at', 'expires_at',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
        'opened_at' => 'datetime',
        'responded_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(Creator::class);
    }

    public function campaignProduct(): BelongsTo
    {
        return $this->belongsTo(CampaignProduct::class);
    }
}
