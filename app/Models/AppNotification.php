<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * Simple in-app notification (uses the pre-existing `notifications` table
 * created in the messaging migration). Model name is prefixed to avoid
 * clashing with Illuminate\Notifications\DatabaseNotification.
 */
class AppNotification extends Model
{
    protected $table = 'notifications';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id', 'recipient_type', 'recipient_id', 'type', 'data', 'read_at',
    ];

    protected $casts = [
        'data' => 'array',
        'read_at' => 'datetime',
    ];

    protected static function booted()
    {
        static::creating(function (self $n) {
            if (! $n->id) $n->id = (string) Str::uuid();
        });
    }

    public static function notifyUser(int $userId, string $type, array $data): self
    {
        return self::create([
            'recipient_type' => 'user',
            'recipient_id' => $userId,
            'type' => $type,
            'data' => $data,
        ]);
    }

    public static function notifyCreator(int $creatorId, string $type, array $data): self
    {
        return self::create([
            'recipient_type' => 'creator',
            'recipient_id' => $creatorId,
            'type' => $type,
            'data' => $data,
        ]);
    }
}
