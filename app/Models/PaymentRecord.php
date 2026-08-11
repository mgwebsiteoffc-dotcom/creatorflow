<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentRecord extends Model
{
    protected $fillable = [
        'workspace_id', 'campaign_id', 'reference', 'description',
        'kind', 'direction', 'amount_cents', 'currency', 'status',
        'method', 'paid_at', 'receipt_url', 'recorded_by',
    ];

    protected $casts = [
        'paid_at' => 'datetime',
        'amount_cents' => 'integer',
    ];

    public function workspace(): BelongsTo { return $this->belongsTo(Workspace::class); }
    public function campaign(): BelongsTo  { return $this->belongsTo(Campaign::class); }
    public function recorder(): BelongsTo  { return $this->belongsTo(User::class, 'recorded_by'); }
}
