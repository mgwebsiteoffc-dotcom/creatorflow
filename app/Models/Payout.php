<?php

namespace App\Models;

use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payout extends Model
{
    use HasUuid;

    protected $fillable = [
        'uuid', 'creator_id', 'workspace_id', 'assignment_id', 'amount_cents',
        'currency', 'platform_fee_cents', 'processing_fee_cents', 'net_cents',
        'method', 'status', 'external_transfer_id', 'scheduled_for', 'paid_at',
        'metadata',
        // RazorpayX bookkeeping
        'provider', 'external_id', 'external_status', 'failure_reason', 'processed_at',
    ];

    protected $casts = [
        'metadata' => 'array',
        'scheduled_for' => 'datetime',
        'paid_at' => 'datetime',
        'processed_at' => 'datetime',
    ];

    protected $attributes = [
        'provider'         => 'manual',
        'external_id'      => null,
        'external_status'  => null,
        'failure_reason'   => null,
        'processed_at'     => null,
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(Creator::class);
    }

    public function workspace(): BelongsTo
    {
        return $this->belongsTo(Workspace::class);
    }

    public function assignment(): BelongsTo
    {
        return $this->belongsTo(\App\Models\CampaignAssignment::class);
    }

    public function getCampaignAttribute(): ?\App\Models\Campaign
    {
        return $this->assignment?->campaign;
    }

    public function markPaid(?string $transferId = null): void
    {
        $this->update([
            'status' => 'paid',
            'paid_at' => now(),
            'external_transfer_id' => $transferId ?? $this->external_transfer_id,
        ]);
    }
}
