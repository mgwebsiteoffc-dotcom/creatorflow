<?php

use App\Jobs\SyncChannel;
use App\Models\Channel;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Schedule;

Schedule::command('inspire')->hourly();

// Nightly full reconcile for every active Shopify channel (catches webhooks
// that were missed during the day).
Schedule::call(function () {
    Channel::where('status', 'active')
        ->where('type', 'shopify')
        ->each(function (Channel $channel) {
            SyncChannel::dispatch($channel->workspace_id, $channel->id, 'products', 'full');
            SyncChannel::dispatch($channel->workspace_id, $channel->id, 'orders', 'incremental');
        });
})->dailyAt('03:15')->name('shopify-nightly-reconcile')->withoutOverlapping();

// Hourly inventory sync.
Schedule::call(function () {
    Channel::where('status', 'active')
        ->where('type', 'shopify')
        ->each(fn (Channel $c) => SyncChannel::dispatch($c->workspace_id, $c->id, 'inventory'));
})->hourly()->name('shopify-inventory-sync')->withoutOverlapping();
