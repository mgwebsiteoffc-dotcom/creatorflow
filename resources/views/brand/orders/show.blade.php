@php $addr = (array) ($order->shipping_address ?? []); @endphp
<x-layouts.app panel="brand" :title="'Order '.($order->order_number ?: '#'.$order->id)">
    <a href="{{ route('brand.orders.index') }}" class="text-sm text-slate-500 hover:text-slate-900">← All orders</a>

    <div class="mt-2 flex flex-wrap items-start justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <h1 class="text-3xl font-black tracking-tight text-slate-900">{{ $order->order_number ?: '#'.$order->id }}</h1>
                <x-badge :tone="in_array($order->status, ['fulfilled','delivered']) ? 'green' : (in_array($order->status, ['cancelled','refunded','returned']) ? 'rose' : 'amber')">{{ $order->status }}</x-badge>
                @if($order->channel && $order->channel->type === 'shopify')<span class="badge-violet">Shopify</span>@endif
            </div>
            <p class="mt-1 text-sm text-slate-500">
                Placed {{ optional($order->placed_at ?: $order->created_at)->format('M j, Y H:i') }}
                @if($assignment) · Campaign: <a href="{{ route('brand.campaigns.show', $assignment->campaign) }}" class="text-violet-700 hover:underline">{{ $assignment->campaign->title }}</a>@endif
            </p>
        </div>
        @if($order->status !== 'cancelled')
            <form method="POST" action="{{ route('brand.orders.cancel', $order) }}" data-confirm="Cancel this order? The creator will be notified.">
                @csrf
                <input type="text" name="reason" placeholder="Reason (optional)" class="input !py-2 !text-xs w-56 mr-2">
                <button class="btn-ghost !py-2 !text-xs !text-rose-600 hover:!bg-rose-50">Cancel order</button>
            </form>
        @endif
    </div>

    <div class="mt-6 grid gap-6 lg:grid-cols-3">
        {{-- Left column · line items --}}
        <section class="lg:col-span-2 rounded-2xl border border-slate-200 bg-white p-6">
            <h2 class="text-lg font-black text-slate-900">Items</h2>
            <div class="mt-4 divide-y divide-slate-100">
                @foreach($order->items as $it)
                    <div class="flex items-center gap-3 py-3">
                        @if($it->product?->primaryImage)
                            <img src="{{ $it->product->primaryImage->path }}" class="h-14 w-14 rounded-lg object-cover">
                        @else
                            <div class="grid h-14 w-14 place-items-center rounded-lg bg-slate-100 text-slate-400"></div>
                        @endif
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-semibold text-slate-900">{{ $it->title }}</p>
                            <p class="text-xs text-slate-500">SKU: {{ $it->sku ?: '—' }} · Qty {{ $it->quantity }}</p>
                        </div>
                        <div class="text-right font-mono text-sm">
                            <p class="line-through text-slate-400">{{ $currentWorkspace->formatMoney($it->price_cents, $order->currency) }}</p>
                            <p class="text-emerald-600 font-bold">Gifted</p>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="mt-4 rounded-xl border border-slate-100 bg-slate-50 p-4 text-sm">
                <div class="flex justify-between"><span class="text-slate-500">Subtotal</span><span class="font-mono">{{ $currentWorkspace->formatMoney($order->subtotal_cents, $order->currency) }}</span></div>
                <div class="flex justify-between text-emerald-600"><span>Barter discount (100 %)</span><span class="font-mono">− {{ $currentWorkspace->formatMoney($order->total_discount_cents, $order->currency) }}</span></div>
                <div class="mt-2 flex justify-between border-t border-slate-200 pt-2 font-bold"><span>Creator pays</span><span class="font-mono">{{ $currentWorkspace->formatMoney($order->total_cents, $order->currency) }}</span></div>
            </div>
        </section>

        {{-- Right column · creator + shipping --}}
        <aside class="space-y-6">
            <section class="rounded-2xl border border-slate-200 bg-white p-6">
                <h2 class="text-lg font-black text-slate-900">Creator</h2>
                @if($order->creator)
                    <div class="mt-4 flex items-center gap-3">
                        <div class="grid h-12 w-12 place-items-center rounded-full bg-gradient-to-br from-violet-500 to-pink-500 text-lg font-bold text-white">{{ strtoupper(substr($order->creator->display_name, 0, 1)) }}</div>
                        <div>
                            <a href="{{ route('brand.creators.show', $order->creator) }}" class="text-sm font-black text-slate-900 hover:underline">{{ $order->creator->display_name }}</a>
                            <p class="text-xs text-slate-500">{{ $order->creator->city }} · {{ number_format($order->creator->follower_count_total) }} followers</p>
                        </div>
                    </div>
                    @if($order->creator->email)
                        <p class="mt-3 text-xs text-slate-500"> <a href="mailto:{{ $order->creator->email }}" class="text-violet-700 hover:underline">{{ $order->creator->email }}</a></p>
                    @endif
                    @if($order->creator->phone)
                        <p class="mt-1 text-xs text-slate-500"> {{ $order->creator->phone }}</p>
                    @endif
                @endif
            </section>

            {{-- Ship to --}}
            <section class="rounded-2xl border border-slate-200 bg-white p-6">
                <h2 class="text-lg font-black text-slate-900">Ship to</h2>
                @if(! empty($addr))
                    <address class="mt-3 not-italic text-sm text-slate-700 leading-relaxed">
                        {{ $addr['line1'] ?? '' }}<br>
                        @if(! empty($addr['line2'])){{ $addr['line2'] }}<br>@endif
                        {{ trim(($addr['city'] ?? '').' '.($addr['postal'] ?? '')) }}<br>
                        {{ $addr['state'] ?? '' }} {{ $addr['country'] ?? '' }}
                        @if(! empty($addr['phone']))<br><span class="text-slate-500"> {{ $addr['phone'] }}</span>@endif
                    </address>
                @elseif($order->creator?->preferences?->shipping_address)
                    <address class="mt-3 not-italic text-sm text-slate-700">
                        @foreach($order->creator->preferences->shipping_address as $line)
                            @if($line){{ $line }}<br>@endif
                        @endforeach
                    </address>
                @else
                    <p class="mt-3 text-sm text-amber-700">No shipping address on file. Add one below.</p>
                @endif
            </section>
        </aside>
    </div>

    {{--  Shipping / fulfillment form  --}}
    <section class="mt-8 rounded-2xl border border-slate-200 bg-white p-6">
        <h2 class="text-lg font-black text-slate-900">Update shipping &amp; status</h2>
        <p class="mt-1 text-xs text-slate-500">Save tracking here — {{ $order->channel?->type === 'shopify' ? 'we\'ll push it to Shopify + the creator will get an automated update.' : 'the creator gets an in-app notification.' }}</p>

        <form method="POST" action="{{ route('brand.orders.updateShipping', $order) }}" class="mt-4 grid gap-4 md:grid-cols-2">
            @csrf
            @method('PATCH')

            <div class="md:col-span-2 grid gap-3 md:grid-cols-3">
                <div>
                    <label class="label">Status</label>
                    <select class="input" name="status">
                        @foreach(['draft','open','paid','fulfilled','delivered','cancelled','refunded','returned'] as $s)
                            <option value="{{ $s }}" @selected($order->status === $s)>{{ ucfirst($s) }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="label">Tracking number</label>
                    <input class="input font-mono" name="tracking_number" value="{{ $order->tracking_number }}" placeholder="AWB / tracking number">
                </div>
                <div>
                    <label class="label">Carrier</label>
                    <input class="input" name="tracking_company" value="{{ $order->tracking_company }}" placeholder="Delhivery, BlueDart, DHL…" list="carriers">
                    <datalist id="carriers">
                        <option value="Delhivery"><option value="BlueDart"><option value="DTDC"><option value="Ekart"><option value="Shiprocket"><option value="XpressBees"><option value="Ecom Express"><option value="India Post"><option value="FedEx"><option value="DHL"><option value="Aramex">
                    </datalist>
                </div>
            </div>

            <div class="md:col-span-2 rounded-xl border border-slate-100 bg-slate-50 p-4">
                <p class="text-xs font-bold uppercase tracking-widest text-slate-500">Shipping address (override)</p>
                <div class="mt-3 grid gap-3 md:grid-cols-2">
                    <div><label class="label">Address line 1</label><input class="input" name="ship_address_line1" value="{{ $addr['line1'] ?? '' }}"></div>
                    <div><label class="label">Address line 2</label><input class="input" name="ship_address_line2" value="{{ $addr['line2'] ?? '' }}"></div>
                    <div><label class="label">City</label><input class="input" name="ship_city" value="{{ $addr['city'] ?? '' }}"></div>
                    <div><label class="label">State</label><input class="input" name="ship_state" value="{{ $addr['state'] ?? '' }}"></div>
                    <div><label class="label">Postal / PIN</label><input class="input" name="ship_postal" value="{{ $addr['postal'] ?? '' }}"></div>
                    <div><label class="label">Country</label><input class="input" name="ship_country" value="{{ $addr['country'] ?? 'India' }}"></div>
                    <div class="md:col-span-2"><label class="label">Phone</label><input class="input" name="ship_phone" value="{{ $addr['phone'] ?? '' }}" placeholder="+91 …"></div>
                </div>
            </div>

            <div class="md:col-span-2 flex justify-end gap-2">
                <a href="{{ route('brand.orders.index') }}" class="btn-ghost">Cancel</a>
                <button class="btn-gradient">Save shipping</button>
            </div>
        </form>
    </section>

    {{-- Timeline --}}
    <section class="mt-8 rounded-2xl border border-slate-200 bg-white p-6">
        <h2 class="text-lg font-black text-slate-900"> Timeline</h2>
        <ol class="mt-4 space-y-3 text-sm">
            <li class="flex gap-3"><span class="grid h-7 w-7 place-items-center rounded-full bg-violet-100 text-violet-700"></span> <div><p class="font-semibold">Order created</p><p class="text-xs text-slate-500">{{ optional($order->placed_at ?: $order->created_at)->format('M j, Y H:i') }}</p></div></li>
            @if($order->tracking_number)
                <li class="flex gap-3"><span class="grid h-7 w-7 place-items-center rounded-full bg-amber-100 text-amber-700"></span> <div><p class="font-semibold">Tracking added</p><p class="text-xs text-slate-500 font-mono">{{ $order->tracking_number }}{{ $order->tracking_company ? ' · '.$order->tracking_company : '' }}</p></div></li>
            @endif
            @if($order->fulfilled_at)
                <li class="flex gap-3"><span class="grid h-7 w-7 place-items-center rounded-full bg-emerald-100 text-emerald-700"></span> <div><p class="font-semibold">Shipped</p><p class="text-xs text-slate-500">{{ $order->fulfilled_at->format('M j, Y H:i') }}</p></div></li>
            @endif
            @if($order->delivered_at)
                <li class="flex gap-3"><span class="grid h-7 w-7 place-items-center rounded-full bg-emerald-100 text-emerald-700"></span> <div><p class="font-semibold">Delivered</p><p class="text-xs text-slate-500">{{ $order->delivered_at->format('M j, Y H:i') }}</p></div></li>
            @endif
            @if($order->status === 'cancelled')
                <li class="flex gap-3"><span class="grid h-7 w-7 place-items-center rounded-full bg-rose-100 text-rose-700"></span> <div><p class="font-semibold">Cancelled</p></div></li>
            @endif
        </ol>
    </section>
</x-layouts.app>
