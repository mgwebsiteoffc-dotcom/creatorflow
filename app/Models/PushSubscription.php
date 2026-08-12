<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PushSubscription extends Model
{
    protected $fillable = [
        'user_id', 'creator_id', 'endpoint', 'p256dh', 'auth_token',
        'user_agent', 'last_seen_at',
    ];

    protected $casts = [
        'last_seen_at' => 'datetime',
    ];

    public function user(): BelongsTo    { return $this->belongsTo(User::class); }
    public function creator(): BelongsTo { return $this->belongsTo(Creator::class); }
}
