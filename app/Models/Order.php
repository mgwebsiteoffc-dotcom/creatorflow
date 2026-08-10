<?php

namespace App\Models;

use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasUuid;

    protected $fillable = [
        'uuid', 'workspace_id', 'channel_id', 'creator_id', 'external_id',
        'order_number', 'email', 'subtotal_cents', 'total_discount_cents',
        'total_cents', 'currency', 'status', 'shipping_address',
        'tracking_number', 'tracking_company', 'placed_at', 'fulfilled_at',
        'delivered_at', 'raw_payload',
    ];

    protected $casts = [
        'shipping_address' => 'array',
        'raw_payload' => 'array',
        'placed_at' => 'datetime',
        'fulfilled_at' => 'datetime',
        'delivered_at' => 'datetime',
    ];

    public function workspace(): BelongsTo
    {
        return $this->belongsTo(Workspace::class);
    }

    public function channel(): BelongsTo
    {
        return $this->belongsTo(Channel::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(Creator::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function attributions(): HasMany
    {
        return $this->hasMany(Attribution::class);
    }

    public function isShipped(): bool
    {
        return in_array($this->status, ['fulfilled'], true) && $this->tracking_number !== null;
    }
}
