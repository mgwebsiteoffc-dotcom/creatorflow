<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChannelWebhook extends Model
{
    protected $fillable = ['channel_id', 'external_id', 'topic', 'address', 'status'];

    public function channel(): BelongsTo
    {
        return $this->belongsTo(Channel::class);
    }
}
