<?php

namespace App\Models;

use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CampaignAssignment extends Model
{
    use HasUuid;

    protected $fillable = [
        'uuid', 'campaign_id', 'campaign_product_id', 'creator_id', 'status',
        'discount_code', 'channel_order_id', 'contract_id', 'content_due_date',
        'fee_cents', 'commission_rate', 'payout_id', 'tracking',
    ];

    protected $casts = [
        'tracking' => 'array',
        'content_due_date' => 'date',
        'commission_rate' => 'decimal:2',
    ];

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }

    public function campaignProduct(): BelongsTo
    {
        return $this->belongsTo(CampaignProduct::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(Creator::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'channel_order_id');
    }

    public function contract(): BelongsTo
    {
        return $this->belongsTo(Contract::class);
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(ContentSubmission::class, 'assignment_id');
    }

    public function payout(): BelongsTo
    {
        return $this->belongsTo(Payout::class);
    }

    public function advance(string $status): void
    {
        $this->update(['status' => $status]);
    }
}
