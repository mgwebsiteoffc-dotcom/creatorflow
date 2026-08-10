<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SyncJob extends Model
{
    protected $table = 'sync_jobs';

    protected $fillable = [
        'channel_id', 'workspace_id', 'type', 'mode', 'status',
        'started_at', 'finished_at', 'processed', 'errors', 'error_log',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
    ];

    public function channel(): BelongsTo
    {
        return $this->belongsTo(Channel::class);
    }

    public function workspace(): BelongsTo
    {
        return $this->belongsTo(Workspace::class);
    }

    public function markRunning(): void
    {
        $this->update(['status' => 'running', 'started_at' => now()]);
    }

    public function markCompleted(int $processed, int $errors = 0): void
    {
        $this->update([
            'status' => 'completed',
            'finished_at' => now(),
            'processed' => $processed,
            'errors' => $errors,
        ]);
    }

    public function markFailed(string $log): void
    {
        $this->update([
            'status' => 'failed',
            'finished_at' => now(),
            'error_log' => $log,
        ]);
    }
}
