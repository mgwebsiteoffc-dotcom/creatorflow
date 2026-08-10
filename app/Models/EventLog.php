<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventLog extends Model
{
    public $timestamps = false;

    protected $table = 'event_log';

    protected $fillable = ['event', 'aggregate_type', 'aggregate_id', 'payload', 'created_at'];

    protected $casts = [
        'payload' => 'array',
        'created_at' => 'datetime',
    ];

    public static function record(string $event, ?string $aggregateType = null, ?int $aggregateId = null, ?array $payload = null): void
    {
        static::create([
            'event' => $event,
            'aggregate_type' => $aggregateType,
            'aggregate_id' => $aggregateId,
            'payload' => $payload,
            'created_at' => now(),
        ]);
    }
}
