<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Channel extends Model
{
    protected $fillable = [
        'workspace_id', 'type', 'name', 'external_id', 'credentials',
        'settings', 'status', 'last_synced_at', 'last_full_sync_at', 'sync_errors',
    ];

    protected $hidden = ['credentials'];

    protected $casts = [
        'credentials' => 'encrypted:array',
        'settings' => 'array',
        'last_synced_at' => 'datetime',
        'last_full_sync_at' => 'datetime',
    ];

    public function workspace(): BelongsTo
    {
        return $this->belongsTo(Workspace::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function webhooks(): HasMany
    {
        return $this->hasMany(ChannelWebhook::class);
    }

    public function isShopify(): bool
    {
        return $this->type === 'shopify';
    }
}
