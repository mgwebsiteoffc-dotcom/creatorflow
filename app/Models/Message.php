<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Message extends Model
{
    protected $fillable = [
        'thread_id', 'sender_type', 'sender_id', 'body', 'attachments',
        'ai_generated', 'delivered_at', 'read_at',
    ];

    protected $casts = [
        'attachments' => 'array',
        'ai_generated' => 'boolean',
        'delivered_at' => 'datetime',
        'read_at' => 'datetime',
    ];

    public function thread(): BelongsTo
    {
        return $this->belongsTo(MessageThread::class, 'thread_id');
    }
}
