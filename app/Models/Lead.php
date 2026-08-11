<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Lead extends Model
{
    protected $fillable = [
        'name', 'email', 'company', 'reason', 'message',
        'source', 'utm_source', 'utm_campaign', 'utm_medium',
        'ip', 'user_agent', 'status', 'note', 'assigned_to',
    ];

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }
}
