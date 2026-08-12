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
                <div data-md-editor>
                    <div class="flex items-center justify-between">
                        <label class="label !mb-0">Brief <span class="ml-1 text-xs font-normal text-slate-400">(supports **bold**, *italic*, lists, links)</span></label>
                        <div class="flex gap-1 rounded-lg border border-slate-200 bg-white p-0.5 text-xs">
                            <button type="button" data-md-mode="write"   class="tab-pill !py-1 !px-2.5 !text-xs is-active">Write</button>
                            <button type="button" data-md-mode="preview" class="tab-pill !py-1 !px-2.5 !text-xs">Preview</button>
                        </div>
                    </div>

                    {{-- Toolbar --}}
                    <div class="mt-2 flex flex-wrap items-center gap-1 rounded-t-xl border border-b-0 border-slate-200 bg-slate-50 p-1.5 text-xs" data-md-toolbar>
                        <button type="button" data-md="h2"  title="Heading"   class="rounded px-2 py-1 font-bold text-slate-700 hover:bg-white">H</button>
                        <button type="button" data-md="b"   title="Bold"      class="rounded px-2 py-1 font-bold text-slate-700 hover:bg-white">B</button>
                        <button type="button" data-md="i"   title="Italic"    class="rounded px-2 py-1 italic text-slate-700 hover:bg-white">I</button>
                        <span class="mx-1 h-4 w-px bg-slate-300"></span>
                        <button type="button" data-md="ul"  title="Bullet list" class="rounded px-2 py-1 text-slate-700 hover:bg-white">• List</button>
                        <button type="button" data-md="ol"  title="Numbered list" class="rounded px-2 py-1 text-slate-700 hover:bg-white">1. List</button>
                        <span class="mx-1 h-4 w-px bg-slate-300"></span>
                        <button type="button" data-md="quote" title="Quote"  class="rounded px-2 py-1 text-slate-700 hover:bg-white">❝</button>
                        <button type="button" data-md="link"  title="Link"   class="rounded px-2 py-1 text-slate-700 hover:bg-white">🔗</button>
                        <button type="button" data-md="code"  title="Code"   class="rounded px-2 py-1 font-mono text-slate-700 hover:bg-white">{`}</button>
                    </div>

                    <textarea data-md-textarea class="input !rounded-t-none min-h-56" name="brief"
                              placeholder="## About the brand&#10;What you make and who it's for.&#10;&#10;## Goal&#10;What success looks like.&#10;&#10;## Do&#10;- Use natural lighting&#10;- Tag the brand&#10;&#10;## Don't&#10;- Overclaim results">{{ old('brief', $suggestion['brief'] ?? '') }}</textarea>

                    <div data-md-preview class="hidden mt-2 min-h-56 rounded-xl border border-slate-200 bg-white p-4"></div>
                </div>

                {{-- ────────────── Audience targeting (invitations use this) ────────────── --}}
                @php
                    $cityOpts   = \App\Support\CreatorTaxonomy::cityOptions();
                    $tierOpts   = \App\Support\CreatorTaxonomy::tiers();
                    $genderOpts = \App\Support\CreatorTaxonomy::genders();
                    $ageOpts    = \App\Support\CreatorTaxonomy::ageRanges();
                    $langOpts   = \App\Support\CreatorTaxonomy::languages();
                @endphp
                <div class="pt-2">
                    <h3 class="font-semibold">🎯 Who should we invite?</h3>
                    <p class="mt-0.5 text-xs text-slate-500">Invitations only go to creators matching these criteria. Leave a group empty to include everyone.</p>
                </div>

                <div class="rounded-2xl border border-violet-100 bg-violet-50/30 p-4 space-y-4">

                    {{-- Cities --}}
                    <div>
                        <div class="mb-1.5 flex items-center justify-between">
                            <label class="label !mb-0">📍 Cities</label>
                            <div class="flex gap-2 text-xs">
                                <button type="button" data-multi-toggle="aud-cities" data-action="all"  class="text-violet-600 hover:underline">All</button>
                                <button type="button" data-multi-toggle="aud-cities" data-action="none" class="text-slate-500 hover:underline">Clear</button>
                            </div>
                        </div>
                        <div class="flex max-h-40 flex-wrap gap-1.5 overflow-y-auto" data-multi-group="aud-cities">
                            @foreach($cityOpts as $slug => $label)
                                <label class="cursor-pointer">
                                    <input type="checkbox" name="audience[cities][]" value="{{ $slug }}" class="peer sr-only">
                                    <span class="chip-body">{{ $label }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    {{-- Tiers --}}
                    <div>
                        <div class="mb-1.5 flex items-center justify-between">
                            <label class="label !mb-0">⭐ Follower tiers</label>
                            <div class="flex gap-2 text-xs">
                                <button type="button" data-multi-toggle="aud-tiers" data-action="all"  class="text-violet-600 hover:underline">All</button>
                                <button type="button" data-multi-toggle="aud-tiers" data-action="none" class="text-slate-500 hover:underline">Clear</button>
                            </div>
                        </div>
                        <div class="grid gap-2 sm:grid-cols-3 lg:grid-cols-5" data-multi-group="aud-tiers">
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

                    <div class="grid gap-4 md:grid-cols-2">
                        {{-- Gender --}}
                        <div>
                            <label class="label">👤 Creator gender</label>
                            <div class="flex flex-wrap gap-1.5">
                                @foreach($genderOpts as $slug => $label)
                                    <label class="cursor-pointer">
                                        <input type="checkbox" name="audience[genders][]" value="{{ $slug }}" class="peer sr-only">
                                        <span class="chip-body">{{ $label }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        {{-- Age --}}
                        <div>
                            <label class="label">🎂 Creator age</label>
                            <div class="flex flex-wrap gap-1.5">
                                @foreach($ageOpts as $slug => $label)
                                    <label class="cursor-pointer">
                                        <input type="checkbox" name="audience[age_ranges][]" value="{{ $slug }}" class="peer sr-only">
                                        <span class="chip-body">{{ $label }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    {{-- Languages --}}
                    <div>
                        <label class="label">🗣️ Languages spoken</label>
                        <div class="flex flex-wrap gap-1.5">
                            @foreach($langOpts as $slug => $label)
                                <label class="cursor-pointer">
                                    <input type="checkbox" name="audience[languages][]" value="{{ $slug }}" class="peer sr-only">
                                    <span class="chip-body">{{ $label }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div class="grid gap-3 md:grid-cols-3">
                        <div>
                            <label class="label">Min followers</label>
                            <input class="input" type="number" name="audience[min_followers]" placeholder="10000">
                        </div>
                        <div>
                            <label class="label">Max followers</label>
                            <input class="input" type="number" name="audience[max_followers]" placeholder="—">
                        </div>
                        <div>
                            <label class="label">Min engagement %</label>
                            <input class="input" type="number" step="0.1" name="audience[min_engagement]" placeholder="3">
                        </div>
                    </div>

                    {{-- Audience gender skew (of the creator's followers) --}}
                    <div class="rounded-xl border border-white/70 bg-white/70 p-3">
                        <p class="text-xs font-semibold text-slate-600">👥 Their audience skew (optional)</p>
                        <div class="mt-2 grid gap-2 md:grid-cols-2">
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
                                <p class="text-xs text-slate-500">{{ $currentWorkspace->formatMoney((int) $product->priceCents()) }} · {{ $product->inventoryTotal() }} in stock
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
                    group.querySelectorAll('input[type="checkbox"]').forEach(cb => cb.checked = checked);
                });
            });
        </script>
    @endif
</x-layouts.app>
