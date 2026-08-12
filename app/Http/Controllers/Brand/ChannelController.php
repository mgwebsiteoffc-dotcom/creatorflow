<?php

namespace App\Http\Controllers\Brand;

use App\Http\Controllers\Controller;
use App\Jobs\SyncChannel;
use App\Models\Channel;
use App\Support\SchemaCheck;
use App\Support\TenantContext;
use Illuminate\Http\Request;

/**
 * Brand-side management of connected commerce channels (Shopify today,
 * WooCommerce/Amazon later). Exposes sync status, one-click resync buttons
 * and inventory snapshot.
 */
class ChannelController extends Controller
{
    public function index(TenantContext $tenant)
    {
        $workspace = $tenant->active();
        $channels  = $workspace->channels()->latest()->get();

        // Inventory + product summary — helpful even when Shopify isn't connected.
        $productCount   = $workspace->products()->count();
        $variantCount   = \App\Models\ProductVariant::whereIn('product_id', $workspace->products()->pluck('id'))->count();
        $inventoryTotal = (int) \App\Models\ProductVariant::whereIn('product_id', $workspace->products()->pluck('id'))->sum('inventory_qty');
        $outOfStock     = (int) \App\Models\ProductVariant::whereIn('product_id', $workspace->products()->pluck('id'))
                            ->where('inventory_qty', '<=', 0)->count();

        // Recent sync jobs for the timeline.
        $recentSyncs = SchemaCheck::has('sync_jobs')
            ? \App\Models\SyncJob::whereIn('channel_id', $channels->pluck('id'))
                ->latest()->take(15)->get()
            : collect();

        return view('brand.channels.index', compact(
            'channels', 'productCount', 'variantCount', 'inventoryTotal',
            'outOfStock', 'recentSyncs'
        ));
    }

    /**
     * Trigger a full or incremental resync for a specific channel.
     */
    public function sync(Channel $channel, Request $request, TenantContext $tenant)
    {
        $this->authorizeChannel($channel, $tenant);

        $type = in_array($request->input('type'), ['products', 'inventory', 'orders'], true)
            ? $request->input('type') : 'products';
        $mode = $request->input('mode') === 'full' ? 'full' : 'incremental';

        SyncChannel::dispatch($channel->workspace_id, $channel->id, $type, $mode);

        return back()->with('status', ucfirst($type)." sync queued ({$mode}). Refresh in ~30 seconds.");
    }

    /**
     * Disconnect a channel — keeps history but stops future syncs.
     */
    public function disconnect(Channel $channel, TenantContext $tenant)
    {
        $this->authorizeChannel($channel, $tenant);

        $channel->update(['status' => 'disconnected']);

        return back()->with('status', "{$channel->name} disconnected. Reinstall from /shopify/install to re-enable.");
    }

    protected function authorizeChannel(Channel $channel, TenantContext $tenant): void
    {
        abort_unless($channel->workspace_id === $tenant->id(), 403);
    }
}
