<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attribution extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'workspace_id', 'order_id', 'creator_id', 'campaign_id',
        'assignment_id', 'model', 'weight', 'revenue_cents', 'currency',
        'attributed_at', 'created_at',
    ];

    protected $casts = [
        'weight' => 'decimal:2',
        'attributed_at' => 'datetime',
        'created_at' => 'datetime',
    ];

    public function workspace(): BelongsTo
    {
        return $this->belongsTo(Workspace::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(Creator::class);
    }

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }
}
