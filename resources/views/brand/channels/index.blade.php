<x-layouts.app panel="brand" title="Connected channels">
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <p class="text-xs font-bold uppercase tracking-widest text-violet-600">Commerce channels</p>
            <h1 class="mt-1 text-3xl font-black tracking-tight text-slate-900">Store connections</h1>
            <p class="mt-1 text-sm text-slate-500">Connect Shopify to auto-sync products + inventory and auto-ship barter orders. Fully manual works too.</p>
        </div>
        <a href="{{ route('shopify.install') }}" class="btn-gradient !py-2 text-sm">Connect Shopify</a>
    </div>

    {{--  Inventory snapshot  --}}
    <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-violet-500 via-purple-500 to-pink-500 p-5 text-white shadow-lg">
            <p class="text-[10px] font-bold uppercase tracking-widest opacity-90">Products</p>
            <p class="mt-2 text-3xl font-black leading-none">{{ number_format($productCount) }}</p>
            <p class="mt-1 text-[11px] opacity-90">In your catalogue</p>
        </div>
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-cyan-500 via-teal-500 to-emerald-500 p-5 text-white shadow-lg">
            <p class="text-[10px] font-bold uppercase tracking-widest opacity-90">Variants</p>
            <p class="mt-2 text-3xl font-black leading-none">{{ number_format($variantCount) }}</p>
            <p class="mt-1 text-[11px] opacity-90">SKU-level items</p>
        </div>
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-amber-500 via-orange-500 to-rose-500 p-5 text-white shadow-lg">
            <p class="text-[10px] font-bold uppercase tracking-widest opacity-90">Total inventory</p>
            <p class="mt-2 text-3xl font-black leading-none">{{ number_format($inventoryTotal) }}</p>
            <p class="mt-1 text-[11px] opacity-90">Units on hand</p>
        </div>
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-slate-700 via-slate-800 to-slate-950 p-5 text-white shadow-lg">
            <p class="text-[10px] font-bold uppercase tracking-widest opacity-90">Out of stock</p>
            <p class="mt-2 text-3xl font-black leading-none">{{ number_format($outOfStock) }}</p>
            <p class="mt-1 text-[11px] opacity-90">SKUs at 0 — can't seed</p>
        </div>
    </div>

    {{--  Connected channels  --}}
    <div class="mt-8">
        <h2 class="text-lg font-black text-slate-900">Your channels</h2>
        <div class="mt-4 space-y-3">
            @forelse($channels as $channel)
                <div class="rounded-2xl border border-slate-200 bg-white p-5">
                    <div class="flex flex-wrap items-start justify-between gap-4">
                        <div class="flex items-center gap-4">
                            <div class="grid h-14 w-14 place-items-center rounded-xl {{ $channel->type === 'shopify' ? 'bg-gradient-to-br from-emerald-400 to-teal-500' : 'bg-gradient-to-br from-slate-500 to-slate-700' }} text-2xl text-white shadow-sm">
                                {{ $channel->type === 'shopify' ? '' : '' }}
                            </div>
                            <div>
                                <div class="flex items-center gap-2">
                                    <p class="text-lg font-black text-slate-900 capitalize">{{ $channel->name ?: $channel->type }}</p>
                                    <x-badge :tone="$channel->status === 'active' ? 'green' : ($channel->status === 'disconnected' ? 'rose' : 'amber')">{{ $channel->status }}</x-badge>
                                    @if($channel->type === 'shopify')<span class="badge-violet">Shopify</span>@endif
                                </div>
                                <p class="mt-0.5 text-xs text-slate-500">
                                    @if($channel->external_id)
                                        <code class="rounded bg-slate-100 px-1.5 py-0.5">{{ $channel->external_id }}</code>
                                    @endif
                                    · {{ $channel->products()->count() }} products
                                    @if($channel->last_synced_at)
                                        · last synced {{ $channel->last_synced_at->diffForHumans() }}
                                    @endif
                                </p>
                            </div>
                        </div>
                        <div class="flex flex-wrap items-center gap-2">
                            @if($channel->status === 'active')
                                <form method="POST" action="{{ route('brand.channels.sync', $channel) }}">
                                    @csrf
                                    <input type="hidden" name="type" value="products">
                                    <input type="hidden" name="mode" value="incremental">
                                    <button class="btn-secondary !py-2 !text-xs">Sync products</button>
                                </form>
                                <form method="POST" action="{{ route('brand.channels.sync', $channel) }}">
                                    @csrf
                                    <input type="hidden" name="type" value="inventory">
                                    <button class="btn-secondary !py-2 !text-xs">Sync inventory</button>
                                </form>
                                <form method="POST" action="{{ route('brand.channels.sync', $channel) }}">
                                    @csrf
                                    <input type="hidden" name="type" value="orders">
                                    <button class="btn-secondary !py-2 !text-xs">Sync orders</button>
                                </form>
                                <form method="POST" action="{{ route('brand.channels.sync', $channel) }}">
                                    @csrf
                                    <input type="hidden" name="type" value="products">
                                    <input type="hidden" name="mode" value="full">
                                    <button class="btn-ghost !py-2 !text-xs">Full resync</button>
                                </form>
                                <form method="POST" action="{{ route('brand.channels.disconnect', $channel) }}" data-confirm="Disconnect {{ $channel->name }}? Sync stops but history stays.">
                                    @csrf
                                    <button class="btn-ghost !py-2 !text-xs !text-rose-600 hover:!bg-rose-50">Disconnect</button>
                                </form>
                            @else
                                <a href="{{ route('shopify.install') }}" class="btn-gradient !py-2 !text-xs">Reconnect</a>
                            @endif
                        </div>
                    </div>

                    @if($channel->type === 'shopify' && $channel->status === 'active')
                        <div class="mt-4 grid gap-3 md:grid-cols-3 text-xs text-slate-600">
                            <div class="rounded-xl border border-slate-100 bg-slate-50 p-3">
                                <p class="font-bold text-slate-800">Product sync</p>
                                <p class="mt-1">New + updated products flow in every 15 min. Fire "Sync products" to pull immediately.</p>
                            </div>
                            <div class="rounded-xl border border-slate-100 bg-slate-50 p-3">
                                <p class="font-bold text-slate-800">Inventory sync</p>
                                <p class="mt-1">Stock levels sync every 30 min so we never seed a creator for out-of-stock SKUs.</p>
                            </div>
                            <div class="rounded-xl border border-slate-100 bg-slate-50 p-3">
                                <p class="font-bold text-slate-800">Auto barter orders</p>
                                <p class="mt-1">When a creator accepts, we auto-create a Shopify draft order with 100 % discount → shipped as normal.</p>
                            </div>
                        </div>
                    @endif
                </div>
            @empty
                <div class="rounded-2xl border border-dashed border-slate-200 bg-slate-50/50 p-8 text-center">
                    <div class="mx-auto grid h-14 w-14 place-items-center rounded-xl bg-gradient-to-br from-emerald-400 to-teal-500 text-2xl text-white shadow-sm"></div>
                    <h3 class="mt-3 text-lg font-black text-slate-900">No stores connected yet</h3>
                    <p class="mt-2 max-w-md mx-auto text-sm text-slate-500">Connect Shopify to auto-sync products + inventory, generate creator discount codes, and auto-ship barter orders.</p>
                    <a href="{{ route('shopify.install') }}" class="btn-gradient mt-5 !py-2 text-sm">Connect Shopify →</a>
                </div>
            @endforelse
        </div>
    </div>

    {{--  Recent sync activity  --}}
    @if($recentSyncs->isNotEmpty())
        <section class="mt-10 rounded-2xl border border-slate-200 bg-white p-6">
            <h2 class="text-lg font-black text-slate-900">Recent sync activity</h2>
            <div class="mt-4 overflow-hidden rounded-xl border border-slate-100">
                <table class="w-full text-sm">
                    <thead class="border-b border-slate-100 bg-slate-50 text-left text-xs uppercase tracking-widest text-slate-500">
                        <tr><th class="p-3">When</th><th class="p-3">Type</th><th class="p-3">Mode</th><th class="p-3">Status</th><th class="p-3 text-right">Items</th></tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($recentSyncs as $s)
                            <tr>
                                <td class="p-3 text-xs text-slate-500">{{ optional($s->started_at ?: $s->created_at)->format('M j, H:i') }}</td>
                                <td class="p-3 capitalize font-semibold text-slate-800">{{ $s->type }}</td>
                                <td class="p-3 capitalize text-slate-600">{{ $s->mode }}</td>
                                <td class="p-3"><x-badge :tone="$s->status === 'completed' ? 'green' : ($s->status === 'failed' ? 'rose' : 'amber')">{{ $s->status }}</x-badge></td>
                                <td class="p-3 text-right font-mono text-slate-700">{{ number_format($s->processed ?? 0) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>
    @endif
</x-layouts.app>
