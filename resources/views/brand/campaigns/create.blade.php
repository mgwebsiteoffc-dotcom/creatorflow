<x-layouts.app panel="brand" title="New campaign">
    <div class="mb-6">
        <a href="{{ route('brand.campaigns.index') }}" class="text-sm text-slate-500 hover:text-slate-800">← Campaigns</a>
        <h1 class="mt-1 text-2xl font-bold">Create campaign</h1>
        <p class="text-sm text-slate-500">Pick products and set how many creators you want per product — this is bulk seeding, natively.</p>
    </div>

    @if($suggestion)
        <div class="mb-6 rounded-2xl border border-violet-200 bg-violet-50 p-4">
            <div class="flex items-center gap-2 font-semibold text-violet-800"><span></span>AI suggestion: {{ $suggestion['title'] }}</div>
            <p class="mt-1 text-sm text-violet-700">{{ $suggestion['summary'] }}</p>
        </div>
    @endif

    @if($products->isEmpty())
        <x-empty-state title="Add products first" icon="products">
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
                <div data-md-editor>
                    <div class="flex items-center justify-between">
                        <label class="label !mb-0">Brief <span class="ml-1 text-xs font-normal text-slate-400">(supports **bold**, *italic*, lists, links)</span></label>
                        <div class="flex gap-1 rounded-lg border border-slate-200 bg-white p-0.5 text-xs">
                            <button type="button" data-md-mode="write" class="tab-pill !py-1 !px-2.5 !text-xs is-active">Write</button>
                            <button type="button" data-md-mode="preview" class="tab-pill !py-1 !px-2.5 !text-xs">Preview</button>
                        </div>
                    </div>

                    {{-- Toolbar --}}
                    <div class="mt-2 flex flex-wrap items-center gap-1 rounded-t-xl border border-b-0 border-slate-200 bg-slate-50 p-1.5 text-xs" data-md-toolbar>
                        <button type="button" data-md="h2" title="Heading" class="rounded px-2 py-1 font-bold text-slate-700 hover:bg-white">H</button>
                        <button type="button" data-md="b" title="Bold" class="rounded px-2 py-1 font-bold text-slate-700 hover:bg-white">B</button>
                        <button type="button" data-md="i" title="Italic" class="rounded px-2 py-1 italic text-slate-700 hover:bg-white">I</button>
                                                <button type="button" data-md="ul" title="Bullet list" class="rounded px-2 py-1 text-slate-700 hover:bg-white">• List</button>
                        <button type="button" data-md="ol" title="Numbered list" class="rounded px-2 py-1 text-slate-700 hover:bg-white">1. List</button>
                                                <button type="button" data-md="quote" title="Quote" class="rounded px-2 py-1 text-slate-700 hover:bg-white"></button>
                        <button type="button" data-md="link" title="Link" class="rounded px-2 py-1 text-slate-700 hover:bg-white"></button>
                        <button type="button" data-md="code" title="Code" class="rounded px-2 py-1 font-mono text-slate-700 hover:bg-white">{`}</button>
                    </div>

                    <textarea data-md-textarea class="input !rounded-t-none min-h-56" name="brief"
                              placeholder="## About the brand&#10;What you make and who it's for.&#10;&#10;## Goal&#10;What success looks like.&#10;&#10;## Do&#10;- Use natural lighting&#10;- Tag the brand&#10;&#10;## Don't&#10;- Overclaim results">{{ old('brief', $suggestion['brief'] ?? '') }}</textarea>

                    <div data-md-preview class="hidden mt-2 min-h-56 rounded-xl border border-slate-200 bg-white p-4"></div>
                </div>

                {{--  Audience targeting (invitations use this)  --}}
                @php
                    $cityOpts = \App\Support\CreatorTaxonomy::cityOptions();
                    $tierOpts = \App\Support\CreatorTaxonomy::tiers();
                    $genderOpts = \App\Support\CreatorTaxonomy::genders();
                    $ageOpts = \App\Support\CreatorTaxonomy::ageRanges();
                    $langOpts = \App\Support\CreatorTaxonomy::languages();
                @endphp
                <div class="pt-2">
                    <h3 class="flex items-center gap-2 font-semibold text-slate-900">
                        <x-icon name="target" class="h-4 w-4 text-violet-600" />
                        Who should we invite?
                    </h3>
                    <p class="mt-0.5 text-xs text-slate-500">Invitations only go to creators matching these criteria. Leave a group empty to include everyone.</p>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-white p-5 space-y-5">

                    {{-- Cities: Meta-ads style searchable multi-select dropdown --}}
                    <div>
                        <label class="label flex items-center gap-1.5">
                            <x-icon name="pin" class="h-3.5 w-3.5 text-slate-400" />
                            Cities
                            <span class="ml-auto text-[11px] font-normal text-slate-400">Type to search · pick as many as you like</span>
                        </label>
                        <x-multi-select
                            name="audience[cities][]"
                            :options="$cityOpts"
                            placeholder="Add a city…"
                            search-placeholder="Search cities…" />
                    </div>

                    {{-- Tiers --}}
                    <div>
                        <div class="mb-2 flex items-center justify-between">
                            <label class="label !mb-0 flex items-center gap-1.5">
                                <x-icon name="star" class="h-3.5 w-3.5 text-slate-400" />
                                Follower tiers
                            </label>
                            <div class="flex gap-3 text-xs">
                                <button type="button" data-multi-toggle="aud-tiers" data-action="all" class="font-semibold text-violet-600 hover:text-violet-800">All</button>
                                <button type="button" data-multi-toggle="aud-tiers" data-action="none" class="text-slate-500 hover:text-slate-800">Clear</button>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-2 sm:grid-cols-3 lg:grid-cols-5" data-multi-group="aud-tiers">
                            @foreach($tierOpts as $slug => $t)
                                <label class="cursor-pointer">
                                    <input type="checkbox" name="audience[tiers][]" value="{{ $slug }}" class="peer sr-only">
                                    <span class="pick-tile-body">
                                        <span class="block text-sm font-semibold text-slate-800">{{ $t['label'] }}</span>
                                        <span class="block text-[10px] text-slate-500">{{ $t['range'] }}</span>
                                    </span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div class="grid gap-5 md:grid-cols-2">
                        {{-- Gender: compact chip row (auto-width, no overlap) --}}
                        <div>
                            <label class="label flex items-center gap-1.5">
                                <x-icon name="user" class="h-3.5 w-3.5 text-slate-400" />
                                Creator gender
                            </label>
                            <div class="flex flex-wrap gap-1.5">
                                @foreach($genderOpts as $slug => $label)
                                    <label class="cursor-pointer">
                                        <input type="checkbox" name="audience[genders][]" value="{{ $slug }}" class="peer sr-only">
                                        <span class="mini-chip">{{ $label }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        {{-- Age: compact chip row --}}
                        <div>
                            <label class="label flex items-center gap-1.5">
                                <x-icon name="cake" class="h-3.5 w-3.5 text-slate-400" />
                                Creator age
                            </label>
                            <div class="flex flex-wrap gap-1.5">
                                @foreach($ageOpts as $slug => $label)
                                    <label class="cursor-pointer">
                                        <input type="checkbox" name="audience[age_ranges][]" value="{{ $slug }}" class="peer sr-only">
                                        <span class="mini-chip tabular-nums">{{ $label }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    {{-- Languages: searchable dropdown like cities --}}
                    <div>
                        <label class="label flex items-center gap-1.5">
                            <x-icon name="languages" class="h-3.5 w-3.5 text-slate-400" />
                            Languages spoken
                        </label>
                        <x-multi-select
                            name="audience[languages][]"
                            :options="$langOpts"
                            placeholder="Add a language…"
                            search-placeholder="Search languages…" />
                    </div>

                    <div class="grid gap-3 border-t border-slate-100 pt-5 md:grid-cols-3">
                        <div>
                            <label class="label">Min followers</label>
                            <input class="input" type="number" name="audience[min_followers]" placeholder="10,000">
                        </div>
                        <div>
                            <label class="label">Max followers</label>
                            <input class="input" type="number" name="audience[max_followers]" placeholder="No limit">
                        </div>
                        <div>
                            <label class="label">Min engagement %</label>
                            <input class="input" type="number" step="0.1" name="audience[min_engagement]" placeholder="3.0">
                        </div>
                    </div>

                    {{-- Audience gender skew (of the creator's followers) --}}
                    <div class="rounded-xl border border-slate-200 bg-slate-50/60 p-4">
                        <p class="flex items-center gap-1.5 text-xs font-semibold text-slate-700">
                            <x-icon name="users" class="h-3.5 w-3.5 text-slate-400" />
                            Their audience skew (optional)
                        </p>
                        <div class="mt-3 grid gap-2 md:grid-cols-2">
                            <select class="input" name="audience[audience_gender]">
                                <option value="">Any audience gender</option>
                                <option value="female">Predominantly female followers</option>
                                <option value="male">Predominantly male followers</option>
                            </select>
                            <div class="flex items-center gap-2">
                                <label class="label !mb-0 whitespace-nowrap">Min %</label>
                                <input class="input" type="number" min="0" max="100" name="audience[audience_min_pct]" placeholder="60">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="pt-2">
                    <div class="flex flex-wrap items-end justify-between gap-2">
                        <div>
                            <h3 class="font-semibold text-slate-900">Products &amp; creator targets</h3>
                            <p class="mt-0.5 text-xs text-slate-500">Pick the products to seed and how many creators should receive each. We invite ~3× based on a 30% acceptance rate.</p>
                        </div>
                        <div class="text-xs text-slate-500">
                            <span data-product-count-selected>0</span>selected · <span>{{ count($products) }} total</span>
                        </div>
                    </div>

                    {{-- Search + scrollable list. Handles 100s of products without breaking the page. --}}
                    <div class="mt-3 overflow-hidden rounded-xl border border-slate-200">
                        <div class="border-b border-slate-100 bg-slate-50 p-2">
                            <div class="relative">
                                <x-icon name="search" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" />
                                <input type="search" data-product-search
                                       class="input !py-2 !pl-9 text-sm"
                                       placeholder="Search {{ count($products) }} products by title or SKU…"
                                       autocomplete="off">
                            </div>
                        </div>

                        <div class="max-h-[380px] overflow-y-auto divide-y divide-slate-100" data-product-list>
                            @foreach($products as $product)
                                @php $seed = collect($suggestion['seed_products'] ?? [])->firstWhere('product_id', $product->id); @endphp
                                <label data-product-row
                                       data-search="{{ strtolower($product->title) }}"
                                       class="product-row flex items-center gap-3 bg-white p-3 transition has-[:checked]:bg-violet-50/50">
                                    <input type="checkbox" class="product-toggle h-4 w-4 shrink-0 rounded border-slate-300 text-violet-600 focus:ring-violet-400"
                                           @checked($seed !== null)>
                                    <input type="hidden" class="product-id" name="products[{{ $loop->index }}][product_id]"
                                           value="{{ $product->id }}" @if($seed === null) disabled @endif>
                                    <div class="min-w-0 flex-1">
                                        <p class="truncate text-sm font-medium text-slate-900">{{ $product->title }}</p>
                                        <p class="truncate text-xs text-slate-500">
                                            {{ $currentWorkspace->formatMoney((int) $product->priceCents()) }} · {{ $product->inventoryTotal() }} in stock
                                            @if($product->hero_score >70) · <span class="badge-amber">Hero {{ $product->hero_score }}</span>@endif
                                        </p>
                                    </div>
                                    <div class="flex items-center gap-1.5">
                                        <label class="text-[10px] font-semibold uppercase tracking-widest text-slate-400">Target</label>
                                        <input type="number" min="1" class="product-target input w-16 !py-1 !text-sm text-center"
                                               name="products[{{ $loop->index }}][target_creators]"
                                               value="{{ $seed['target_creators'] ?? 10 }}"
                                               @if($seed === null) disabled @endif>
                                    </div>
                                </label>
                            @endforeach
                            <div data-product-empty class="hidden p-6 text-center text-sm text-slate-500">No products match your search.</div>
                        </div>
                    </div>
                </div>

                <script>
                    (function () {
                        // Live filter of the products list + running "selected" counter.
                        const search = document.querySelector('[data-product-search]');
                        const list = document.querySelector('[data-product-list]');
                        const empty = document.querySelector('[data-product-empty]');
                        const rows = list ? list.querySelectorAll('[data-product-row]') : [];
                        const counter = document.querySelector('[data-product-count-selected]');
                        if (! list) return;

                        const filter = () => {
                            const q = (search?.value || '').trim().toLowerCase();
                            let visible = 0;
                            rows.forEach(r => {
                                const match = ! q || r.dataset.search.includes(q);
                                r.style.display = match ? '' : 'none';
                                if (match) visible++;
                            });
                            if (empty) empty.classList.toggle('hidden', visible >0);
                        };
                        const updateCounter = () => {
                            if (! counter) return;
                            counter.textContent = list.querySelectorAll('input.product-toggle:checked').length;
                        };

                        search?.addEventListener('input', filter);
                        rows.forEach(r =>r.querySelector('input.product-toggle')?.addEventListener('change', updateCounter));
                        updateCounter();
                    })();
                </script>
            </div>

            <div class="card h-fit space-y-4 p-5">
                <h3 class="font-semibold">Budget &amp; timeline</h3>
                <div>
                    <label class="label">Creator fee per post (₹)</label>
                    <input class="input" type="number" step="0.01" min="0" name="creator_fee" value="0.00" placeholder="e.g. 2500.00">
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
                        <p class="text-slate-600">
                            {{ $suggestion['predicted']['content_assets'] ?? '—' }} content assets ·
                            ROI
                            @if(is_numeric($suggestion['predicted']['roi_p50'] ?? null))
                                {{ number_format((float) $suggestion['predicted']['roi_p50'], 1) }}×
                            @else — @endif
                        </p>
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

            // Audience multi-select bulk toggles
            document.querySelectorAll('[data-multi-toggle]').forEach(btn => {
                btn.addEventListener('click', () => {
                    const group = document.querySelector(`[data-multi-group="${btn.dataset.multiToggle}"]`);
                    if (!group) return;
                    const checked = btn.dataset.action === 'all';
                    group.querySelectorAll('input[type="checkbox"]').forEach(cb =>cb.checked = checked);
                });
            });
        </script>
    @endif
</x-layouts.app>
