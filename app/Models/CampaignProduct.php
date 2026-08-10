<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CampaignProduct extends Model
{
    protected $fillable = [
        'campaign_id', 'product_id', 'variant_id', 'target_creators',
        'accepted_count', 'shipped_count', 'content_received_count',
        'fee_cents', 'commission_rate', 'settings',
    ];

    protected $casts = [
        'settings' => 'array',
        'commission_rate' => 'decimal:2',
    ];

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class);
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(CampaignAssignment::class, 'campaign_product_id');
    }

    public function openSlots(): int
    {
        return max(0, $this->target_creators - $this->accepted_count);
    }
}
