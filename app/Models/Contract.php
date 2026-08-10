<?php

namespace App\Models;

use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Contract extends Model
{
    use HasUuid;

    protected $fillable = [
        'uuid', 'workspace_id', 'campaign_id', 'creator_id', 'title',
        'body', 'usage_rights', 'fee_cents', 'status', 'signed_by_creator_at',
        'signed_by_brand_at', 'signature_ip', 'expires_at',
    ];

    protected $casts = [
        'usage_rights' => 'array',
        'signed_by_creator_at' => 'datetime',
        'signed_by_brand_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function workspace(): BelongsTo
    {
        return $this->belongsTo(Workspace::class);
    }

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(Creator::class);
    }

    public function signAsCreator(?string $ip = null): void
    {
        $this->update([
            'status' => 'signed',
            'signed_by_creator_at' => now(),
            'signature_ip' => $ip,
        ]);
    }
}
