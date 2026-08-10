<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Invoice extends Model
{
    protected $fillable = [
        'workspace_id', 'subscription_id', 'provider', 'provider_invoice_id',
        'amount_cents', 'currency', 'status', 'due_at', 'paid_at', 'pdf_path',
    ];

    protected $casts = [
        'due_at' => 'datetime',
        'paid_at' => 'datetime',
    ];

    public function workspace(): BelongsTo
    {
        return $this->belongsTo(Workspace::class);
    }

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }
}
