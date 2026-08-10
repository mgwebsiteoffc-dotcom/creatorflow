<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MessageThreadParticipant extends Model
{
    public $timestamps = false;

    protected $table = 'message_thread_participants';

    protected $fillable = [
        'thread_id', 'participant_type', 'participant_id',
        'last_read_at', 'muted',
    ];

    protected $casts = [
        'last_read_at' => 'datetime',
        'muted' => 'boolean',
    ];

    public function thread(): BelongsTo
    {
        return $this->belongsTo(MessageThread::class, 'thread_id');
    }
}
