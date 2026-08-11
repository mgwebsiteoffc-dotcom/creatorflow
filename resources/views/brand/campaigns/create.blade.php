<x-layouts.app panel="brand" title="New campaign">
    <div class="mb-6">
        <a href="{{ route('brand.campaigns.index') }}" class="text-sm text-slate-500 hover:text-slate-800">← Campaigns</a>
        <h1 class="mt-1 text-2xl font-bold">Create campaign</h1>
        <p class="text-sm text-slate-500">Pick products and set how many creators you want per product — this is bulk seeding, natively.</p>
    </div>

    @if($suggestion)
        <div class="mb-6 rounded-2xl border border-violet-200 bg-violet-50 p-4">
            <div class="flex items-center gap-2 font-semibold text-violet-800"><span>✨</span> AI suggestion: {{ $suggestion['title'] }}</div>
            <p class="mt-1 text-sm text-violet-700">{{ $suggestion['summary'] }}</p>
        </div>
    @endif

    @if($products->isEmpty())
        <x-empty-state title="Add products first" icon="📦">
            You need products before creating a campaign.
            <x-slot:action><a href="{{ route('brand.onboarding') }}" class="btn-primary">Add products</a></x-slot:action>
        </x-empty-state>
    @else
        <form method="POST" action="{{ route('brand.campaigns.store') }}" class="grid gap-5 lg:grid-cols-3">
            @csrf
            <div class="card space-y-4 p-5 lg:col-span-2">
                <div>
                    <label class="label">Campaign title</label>
                    <input class="input" name="title" required value="{{ old('title', $suggestion['title'] ?? '') }}">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="label">Type</label>
                        <select class="input" name="type">
                            @foreach(['barter' => 'Barter (gift product)', 'paid' => 'Paid', 'affiliate' => 'Affiliate', 'hybrid' => 'Hybrid'] as $v => $l)
                                <option value="{{ $v }}" @selected(($suggestion['type'] ?? 'barter') === $v)>{{ $l }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="label">Niche</label>
                        <input class="input" name="niche" value="{{ old('niche', $suggestion['niche'] ?? '') }}">
                    </div>
                </div>
                <div>
                    <label class="label">Brief (Markdown)</label>
                    <textarea class="input min-h-40" name="brief">{{ old('brief', $suggestion['brief'] ?? '') }}</textarea>
                </div>

                <h3 class="pt-2 font-semibold">Products &amp; creator targets</h3>
                <p class="text-xs text-slate-500">Set how many creators should receive each product. We automatically invite ~3x based on a 30% acceptance rate and maintain a waitlist.</p>

                <div class="space-y-2">
                    @foreach($products as $product)
                        @php $seed = collect($suggestion['seed_products'] ?? [])->firstWhere('product_id', $product->id); @endphp
                        <label class="product-row flex items-center gap-3 rounded-xl border border-slate-200 p-3 transition has-[:checked]:border-violet-400 has-[:checked]:bg-violet-50/40">
                            <input type="checkbox" class="product-toggle h-4 w-4 rounded border-slate-300 text-violet-600 focus:ring-violet-400"
                                   @checked($seed !== null)>
                            <input type="hidden" class="product-id" name="products[{{ $loop->index }}][product_id]"
                                   value="{{ $product->id }}" @if($seed === null) disabled @endif>
                            <div class="min-w-0 flex-1">
                                <p class="truncate font-medium">{{ $product->title }}</p>
                                <p class="text-xs text-slate-500">${{ number_format($product->priceCents()/100, 2) }} · {{ $product->inventoryTotal() }} in stock
                                    @if($product->hero_score > 70) · <span class="badge-amber">Hero {{ $product->hero_score }}</span>@endif
                                </p>
                            </div>
                            <input type="number" min="1" class="product-target input w-20 !py-1.5 text-center"
                                   name="products[{{ $loop->index }}][target_creators]"
                                   value="{{ $seed['target_creators'] ?? 10 }}"
                                   @if($seed === null) disabled @endif>
                        </label>
                    @endforeach
                </div>
            </div>

            <div class="card h-fit space-y-4 p-5">
                <h3 class="font-semibold">Budget &amp; timeline</h3>
                <div>
                    <label class="label">Creator fee per post (cents)</label>
                    <input class="input" type="number" name="creator_fee_cents" value="0">
                    <p class="mt-1 text-xs text-slate-500">0 for pure barter.</p>
                </div>
                <div>
                    <label class="label">Assumed acceptance rate %</label>
                    <input class="input" type="number" name="acceptance_rate_assumed" value="{{ $suggestion['acceptance_rate_assumed'] ?? 30 }}">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div><label class="label">Start</label><input class="input" type="date" name="start_date"></div>
                    <div><label class="label">End</label><input class="input" type="date" name="end_date"></div>
                </div>
                @if($suggestion)
                    <div class="rounded-xl bg-slate-50 p-3 text-sm">
                        <p class="font-semibold">AI prediction</p>
                        <p class="text-slate-600">{{ $suggestion['predicted']['content_assets'] ?? '—' }} content assets · ROI {{ $suggestion['predicted']['roi_p50'] ?? '—' }}x</p>
                    </div>
                @endif
                <button class="btn-primary w-full">Create draft</button>
            </div>
        </form>

        <script>
            document.querySelectorAll('.product-row').forEach(row => {
                const cb = row.querySelector('.product-toggle');
                const sync = () => {
                    row.querySelectorAll('.product-id, .product-target').forEach(el => { el.disabled = !cb.checked; });
                };
                cb.addEventListener('change', sync);
                sync();
            });
        </script>
    @endif
</x-layouts.app>
