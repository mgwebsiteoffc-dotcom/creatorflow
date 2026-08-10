<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AiRun extends Model
{
    protected $table = 'ai_runs';

    protected $fillable = [
        'workspace_id', 'subject_type', 'subject_id', 'task', 'model',
        'prompt', 'response', 'tokens_in', 'tokens_out', 'cost_cents',
        'status', 'latency_ms',
    ];

    protected $casts = [
        'latency_ms' => 'integer',
        'tokens_in' => 'integer',
        'tokens_out' => 'integer',
    ];

    public function workspace(): BelongsTo
    {
        return $this->belongsTo(Workspace::class);
    }

    public function markCompleted(?string $response, int $tokensIn = 0, int $tokensOut = 0, int $costCents = 0, int $latencyMs = 0): void
    {
        $this->update([
            'status' => 'completed',
            'response' => $response,
            'tokens_in' => $tokensIn,
            'tokens_out' => $tokensOut,
            'cost_cents' => $costCents,
            'latency_ms' => $latencyMs,
        ]);
    }
}
