<x-layouts.app panel="brand" title="Orders">
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <p class="text-xs font-bold uppercase tracking-widest text-violet-600">Barter &amp; seeding</p>
            <h1 class="mt-1 text-3xl font-black tracking-tight text-slate-900">Orders</h1>
            <p class="mt-1 text-sm text-slate-500">Ship product to creators, add tracking, and mark delivered — either through Shopify or manually.</p>
        </div>
        <a href="{{ route('brand.channels.index') }}" class="btn-secondary !py-2 text-sm">Manage channels →</a>
    </div>

    {{-- Stat band --}}
    <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="rounded-2xl bg-gradient-to-br from-violet-500 to-pink-500 p-5 text-white shadow-lg">
            <p class="text-[10px] font-bold uppercase tracking-widest opacity-90">All orders</p>
            <p class="mt-2 text-3xl font-black leading-none">{{ number_format($stats['total']) }}</p>
        </div>
        <div class="rounded-2xl bg-gradient-to-br from-amber-500 to-rose-500 p-5 text-white shadow-lg">
            <p class="text-[10px] font-bold uppercase tracking-widest opacity-90">Pending ship</p>
            <p class="mt-2 text-3xl font-black leading-none">{{ number_format($stats['pending']) }}</p>
        </div>
        <div class="rounded-2xl bg-gradient-to-br from-emerald-500 to-teal-500 p-5 text-white shadow-lg">
            <p class="text-[10px] font-bold uppercase tracking-widest opacity-90">Fulfilled</p>
            <p class="mt-2 text-3xl font-black leading-none">{{ number_format($stats['fulfilled']) }}</p>
        </div>
        <div class="rounded-2xl bg-gradient-to-br from-sky-500 to-indigo-500 p-5 text-white shadow-lg">
            <p class="text-[10px] font-bold uppercase tracking-widest opacity-90">Awaiting order</p>
            <p class="mt-2 text-3xl font-black leading-none">{{ number_format($stats['needs_ship']) }}</p>
        </div>
    </div>

    {{-- Assignments waiting on an order (never got auto-shipped) --}}
    @if($needsFulfillment->isNotEmpty())
        <section class="mt-8 rounded-2xl border border-amber-200 bg-amber-50/50 p-5 md:p-6">
            <div class="flex items-center gap-2">
                <span class="grid h-9 w-9 place-items-center rounded-xl bg-gradient-to-br from-amber-500 to-orange-500 text-white shadow-sm">⚠</span>
                <div>
                    <h2 class="text-lg font-black text-slate-900">{{ $needsFulfillment->count() }} creators waiting on their order</h2>
                    <p class="text-xs text-slate-500">These creators accepted your invitation but no order has been created yet.</p>
                </div>
            </div>
            <div class="mt-4 space-y-2">
                @foreach($needsFulfillment as $a)
                    <div class="flex flex-wrap items-center gap-3 rounded-xl border border-slate-200 bg-white p-3">
                        <div class="grid h-10 w-10 place-items-center rounded-full bg-gradient-to-br from-violet-500 to-pink-500 text-sm font-bold text-white">{{ strtoupper(substr($a->creator->display_name, 0, 1)) }}</div>
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-semibold text-slate-900">{{ $a->creator->display_name }}
                                <span class="text-xs font-normal text-slate-500">· {{ $a->campaign->title }}</span>
                            </p>
                            <p class="truncate text-xs text-slate-500">
                                {{ $a->campaignProduct->product->title ?? '—' }}
                                @if($a->creator->preferences?->shipping_address)
                                    · Ships to {{ collect($a->creator->preferences->shipping_address)->filter()->first() ?? '—' }}
                                @else
                                    · No shipping address on file
                                @endif
                            </p>
                        </div>
                        <form method="POST" action="{{ route('brand.orders.createFromAssignment', $a) }}">
                            @csrf
                            <button class="btn-primary !py-2 !text-xs">🎁 Create order</button>
                        </form>
                    </div>
                @endforeach
            </div>
        </section>
    @endif

    {{-- Filter --}}
    <form method="GET" class="mt-8 flex flex-wrap items-center gap-2">
        @foreach(['all','draft','open','paid','fulfilled','delivered','cancelled','refunded'] as $s)
            <button type="submit" name="status" value="{{ $s }}"
                    class="rounded-full border {{ $status === $s ? 'border-violet-500 bg-gradient-to-r from-violet-500 to-pink-500 text-white shadow-sm' : 'border-slate-200 bg-white text-slate-600 hover:border-violet-300' }} px-3 py-1.5 text-xs font-semibold capitalize">
                {{ $s }}
            </button>
        @endforeach
    </form>

    {{-- Orders table --}}
    <div class="mt-4 overflow-x-auto rounded-2xl border border-slate-200 bg-white">
        <table class="w-full min-w-[640px] text-sm">
            <thead class="border-b border-slate-200 bg-slate-50 text-left text-xs uppercase tracking-widest text-slate-500">
                <tr>
                    <th class="p-3">Order</th>
                    <th class="p-3">Creator</th>
                    <th class="p-3">Product</th>
                    <th class="p-3">Tracking</th>
                    <th class="p-3">Status</th>
                    <th class="p-3 text-right">Placed</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($orders as $o)
                    <tr class="transition hover:bg-violet-50/30">
                        <td class="p-3">
                            <a href="{{ route('brand.orders.show', $o) }}" class="font-semibold text-slate-800 hover:text-violet-700">
                                {{ $o->order_number ?: '#'.$o->id }}
                            </a>
                            @if($o->channel && $o->channel->type === 'shopify')<br><span class="text-[10px] uppercase tracking-widest text-emerald-600">Shopify</span>@endif
                        </td>
                        <td class="p-3">
                            <div class="flex items-center gap-2">
                                <div class="grid h-8 w-8 place-items-center rounded-full bg-gradient-to-br from-violet-500 to-pink-500 text-xs font-bold text-white">{{ strtoupper(substr($o->creator->display_name ?? '?', 0, 1)) }}</div>
                                <div>
                                    <p class="text-sm font-semibold text-slate-800">{{ $o->creator?->display_name ?? '—' }}</p>
                                    <p class="text-[11px] text-slate-500">{{ $o->creator?->city ?? '' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="p-3 text-sm text-slate-600">
                            @if($o->items->isNotEmpty())
                                {{ $o->items->first()->title }}
                                @if($o->items->count() > 1) <span class="text-xs text-slate-400">+{{ $o->items->count() - 1 }} more</span>@endif
                            @else — @endif
                        </td>
                        <td class="p-3 text-xs">
                            @if($o->tracking_number)
                                <span class="font-mono text-slate-700">{{ $o->tracking_number }}</span>
                                @if($o->tracking_company)<span class="text-slate-400"> · {{ $o->tracking_company }}</span>@endif
                            @else
                                <span class="text-slate-400">—</span>
                            @endif
                        </td>
                        <td class="p-3">
                            <x-badge :tone="in_array($o->status, ['fulfilled','delivered']) ? 'green' : (in_array($o->status, ['cancelled','refunded','returned']) ? 'rose' : 'amber')">{{ $o->status }}</x-badge>
                        </td>
                        <td class="p-3 text-right text-xs text-slate-500">{{ optional($o->placed_at ?: $o->created_at)->format('M j') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="p-8 text-center">
                            <p class="text-4xl">📦</p>
                            <p class="mt-3 text-sm text-slate-500">No orders yet. Once creators accept your campaign, their orders will appear here.</p>
                            <a href="{{ route('brand.campaigns.index') }}" class="btn-secondary mt-4 !py-2 text-xs">View campaigns →</a>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">{{ $orders->links() }}</div>
</x-layouts.app>
