<?php

namespace App\Jobs;

use App\Domains\Commerce\Channels\ChannelRegistry;
use App\Models\Channel;
use App\Models\SyncJob;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SyncChannel implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 2;

    public function __construct(
        public int $workspaceId,
        public ?int $channelId = null,
        public string $type = 'products',
        public string $mode = 'incremental',
    ) {}

    public function handle(ChannelRegistry $registry): void
    {
        $channel = $this->channelId
            ? Channel::with('workspace')->findOrFail($this->channelId)
            : Channel::where('workspace_id', $this->workspaceId)->where('status', 'active')->first();

        if (! $channel) {
            return;
        }

        $sync = SyncJob::create([
            'channel_id' => $channel->id,
            'workspace_id' => $channel->workspace_id,
            'type' => $this->type,
            'mode' => $this->mode,
            'status' => 'running',
            'started_at' => now(),
        ]);

        $adapter = $registry->forModel($channel);

        try {
            $result = match ($this->type) {
                'products' => $adapter->syncProducts($channel->workspace, $sync),
                'inventory' => $adapter->syncInventory($channel->workspace, $sync),
                'orders' => $adapter->syncOrders($channel->workspace, now()->subDays(30), $sync),
                default => throw new \InvalidArgumentException("Unknown sync type [{$this->type}]"),
            };

            $sync->markCompleted($result->processed(), $result->errorCount());

            if ($this->type === 'products' && $this->mode === 'full') {
                $channel->update(['last_full_sync_at' => now()]);
            }
        } catch (\Throwable $e) {
            $sync->markFailed($e->getMessage());
            $channel->increment('sync_errors');
            throw $e;
        }
    }
}
