<?php

namespace App\Domains\Commerce\Channels;

use App\Domains\Commerce\Channels\Contracts\CommerceChannel;
use App\Models\Channel;
use App\Models\Workspace;
use InvalidArgumentException;

/**
 * Resolves a CommerceChannel implementation from a channel type.
 *
 * Adapters are registered in CommerceServiceProvider. Adding a new sales
 * channel (WooCommerce, Amazon...) means implementing CommerceChannel and
 * registering it here — no domain code changes required.
 */
class ChannelRegistry
{
    /**
     * @param  array<string, class-string<CommerceChannel>>  $channels
     */
    public function __construct(protected array $channels = []) {}

    public function register(string $type, string $implementation): void
    {
        $this->channels[$type] = $implementation;
    }

    public function make(string $type): CommerceChannel
    {
        if (! isset($this->channels[$type])) {
            throw new InvalidArgumentException("No channel registered for type [{$type}].");
        }

        return app($this->channels[$type]);
    }

    public function forWorkspace(Workspace $workspace, ?string $preferredType = null): CommerceChannel
    {
        $channel = $preferredType
            ? $workspace->channels()->where('type', $preferredType)->first()
            : $workspace->channels()->where('status', 'active')->orderBy('id')->first();

        if (! $channel) {
            // No connected store — fall back to the manual channel so the
            // unified experience still works for web-only brands.
            return $this->make('manual');
        }

        return $this->make($channel->type);
    }

    public function forModel(Channel $channel): CommerceChannel
    {
        return $this->make($channel->type);
    }

    /**
     * @return array<string, class-string<CommerceChannel>>
     */
    public function all(): array
    {
        return $this->channels;
    }
}
