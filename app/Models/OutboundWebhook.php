<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OutboundWebhook extends Model
{
    protected $fillable = ['workspace_id', 'url', 'secret', 'events', 'active'];

    protected $casts = [
        'events' => 'array',
        'active' => 'boolean',
    ];

    public function workspace(): BelongsTo
    {
        return $this->belongsTo(Workspace::class);
    }
}
