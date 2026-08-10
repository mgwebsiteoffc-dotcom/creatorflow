<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MarketplaceCommission extends Model
{
    protected $fillable = [
        'workspace_id', 'assignment_id', 'order_id', 'amount_cents',
        'currency', 'rate', 'status',
    ];

    protected $casts = ['rate' => 'decimal:2'];

    public function workspace(): BelongsTo
    {
        return $this->belongsTo(Workspace::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
