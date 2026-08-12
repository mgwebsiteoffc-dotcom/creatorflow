<?php

namespace App\Models;

use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Model;

class AbExperiment extends Model
{
    use HasUuid;

    protected $fillable = ['uuid','name','slug','surface','goal_event','variants','status','started_at','ended_at'];
    protected $casts = ['variants' => 'array', 'started_at' => 'datetime', 'ended_at' => 'datetime'];

    /**
     * Deterministically pick a variant for a visitor id based on the configured weights.
     */
    public function pickFor(string $visitorId): array
    {
        $variants = $this->variants ?: [['key' => 'A', 'weight' => 100]];
        // Hash the visitor id + experiment slug to a 0-99 bucket.
        $bucket = hexdec(substr(md5($this->slug.'|'.$visitorId), 0, 4)) % 100;
        $cumulative = 0;
        foreach ($variants as $v) {
            $cumulative += (int) ($v['weight'] ?? 0);
            if ($bucket < $cumulative) return $v;
        }
        return $variants[0];
    }

    public function isRunning(): bool { return $this->status === 'running'; }
}
