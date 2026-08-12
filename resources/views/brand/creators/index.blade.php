<x-layouts.app panel="brand" title="Creator marketplace">
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <h1 class="text-3xl font-black tracking-tight text-slate-900">Creator marketplace</h1>
            <p class="mt-1 text-sm text-slate-500">Search by <strong>city</strong> or <strong>tier</strong>, add audience filters, then invite to a campaign.</p>
        </div>
        <div class="text-xs text-slate-500">
            <span class="inline-flex items-center gap-1"><span class="h-2 w-2 rounded-full bg-emerald-500"></span> {{ number_format($creators->total()) }} match</span>
        </div>
    </div>

    <form method="GET" class="card mt-6 p-5 space-y-5">
        {{-- Mode switch: search by City OR by Tier --}}
        <div class="flex flex-wrap items-center gap-3">
            <span class="text-xs font-semibold uppercase tracking-wider text-slate-500">Search by</span>
            <div class="inline-flex rounded-xl border border-slate-200 bg-slate-50 p-1 text-sm">
                <label class="cursor-pointer">
                    <input type="radio" name="mode" value="city" class="peer sr-only" @checked($mode === 'city')>
                    <span class="block rounded-lg px-4 py-1.5 peer-checked:bg-white peer-checked:shadow peer-checked:text-violet-700 text-slate-500">📍 City</span>
                </label>
                <label class="cursor-pointer">
                    <input type="radio" name="mode" value="tier" class="peer sr-only" @checked($mode === 'tier')>
                    <span class="block rounded-lg px-4 py-1.5 peer-checked:bg-white peer-checked:shadow peer-checked:text-violet-700 text-slate-500">⭐ Tier</span>
                </label>
            </div>

            <div class="ml-auto flex flex-wrap items-center gap-2">
                <input type="search" name="q" value="{{ request('q') }}" placeholder="Search name / bio / city…" class="input !py-2 w-64">
                <button class="btn-primary !py-2">Apply</button>
                @if(request()->hasAny(['q','cities','tiers','genders','ages','languages','niche','min_followers','max_followers','min_engagement','barter','paid']))
                    <a href="{{ route('brand.creators.index') }}" class="btn-ghost !py-2 text-sm">Reset</a>
                @endif
            </div>
        </div>

        {{-- City picker (multi-select dropdown) --}}
        <div data-panel="city" class="{{ $mode === 'city' ? '' : 'hidden' }}">
            <label class="label">Cities <span class="ml-1 text-xs font-normal text-slate-400">— search &amp; pick as many as you like</span></label>

            <div class="relative" data-ms-wrap>
                {{-- Trigger button + selected pills --}}
                <button type="button" data-ms-trigger
                        class="input flex min-h-[46px] w-full flex-wrap items-center gap-1.5 text-left">
                    <span data-ms-empty class="text-sm text-slate-400 {{ count($cities) ? 'hidden' : '' }}">Pick one or more cities…</span>
                    @foreach($cities as $slug)
                        @if(isset($cityOptions[$slug]))
                            <span data-ms-pill="{{ $slug }}" class="inline-flex items-center gap-1 rounded-md bg-violet-100 px-2 py-0.5 text-xs font-semibold text-violet-800">
                                <span>{{ $cityOptions[$slug] }}</span>
                                <button type="button" data-ms-remove="{{ $slug }}" class="text-violet-500 hover:text-violet-900" aria-label="Remove">×</button>
                            </span>
                        @endif
                    @endforeach
                    <svg class="ml-auto h-4 w-4 shrink-0 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" d="M6 9l6 6 6-6"/></svg>
                </button>

                {{-- Panel (dropdown) --}}
                <div data-ms-panel class="absolute left-0 right-0 top-full z-40 mt-2 hidden max-h-80 overflow-hidden rounded-xl border border-slate-200 bg-white shadow-2xl">
                    <div class="border-b border-slate-100 p-2">
                        <input data-ms-search type="search" class="input !py-2 text-sm" placeholder="Search city…" autocomplete="off">
                    </div>
                    <div class="flex items-center justify-between border-b border-slate-100 bg-slate-50 px-3 py-1.5 text-[11px] font-semibold text-slate-500">
                        <span data-ms-count>{{ count($cities) }} selected</span>
                        <div class="flex gap-2">
                            <button type="button" data-ms-all class="text-violet-600 hover:underline">Select all</button>
                            <button type="button" data-ms-clear class="text-slate-500 hover:underline">Clear</button>
                        </div>
                    </div>
                    <div class="max-h-60 overflow-y-auto p-1" data-ms-list>
                        @foreach($cityOptions as $slug => $label)
                            <label data-ms-item="{{ strtolower($label) }}"
                                   class="flex cursor-pointer items-center gap-2 rounded-lg px-3 py-2 text-sm text-slate-700 hover:bg-violet-50">
                                <input type="checkbox" name="cities[]" value="{{ $slug }}"
                                       data-ms-value="{{ $slug }}"
                                       data-ms-label="{{ $label }}"
                                       class="h-4 w-4 rounded border-slate-300 text-violet-600 focus:ring-violet-400"
                                       @checked(in_array($slug, $cities))>
                                <span>{{ $label }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        {{-- Tier picker (multi) --}}
        <div data-panel="tier" class="{{ $mode === 'tier' ? '' : 'hidden' }}">
            <div class="mb-2 flex items-center justify-between">
                <label class="label !mb-0">Follower tiers</label>
                <div class="flex gap-2 text-xs">
                    <button type="button" data-multi-toggle="tiers" data-action="all"  class="text-violet-600 hover:underline">Select all</button>
                    <button type="button" data-multi-toggle="tiers" data-action="none" class="text-slate-500 hover:underline">Clear</button>
                </div>
            </div>
            <div class="grid gap-2 sm:grid-cols-2 lg:grid-cols-5" data-multi-group="tiers">
                @foreach($tierOptions as $slug => $t)
                    <label class="pick-tile cursor-pointer">
                        <input type="checkbox" name="tiers[]" value="{{ $slug }}" class="peer sr-only" @checked(in_array($slug, $tiers))>
                        <div class="pick-tile-body">
                            <div class="flex items-center justify-between">
                                <span class="font-semibold text-slate-800">{{ $t['label'] }}</span>
                                <span class="text-[10px] text-slate-400">{{ $t['range'] }}</span>
                            </div>
                            <p class="mt-1 text-xs text-slate-500">{{ $t['note'] }}</p>
                        </div>
                    </label>
                @endforeach
            </div>
        </div>

        {{-- Audience refiners --}}
        <details class="rounded-xl border border-slate-200 bg-white/50 p-4">
            <summary class="cursor-pointer text-sm font-semibold text-slate-700">More filters — niche, gender, age, language, engagement</summary>

            <div class="mt-4 grid gap-4 md:grid-cols-2">
                <div>
                    <label class="label">Niche</label>
                    <select class="input" name="niche">
                        <option value="">All niches</option>
                        @foreach($niches as $n)
                            <option value="{{ $n }}" @selected(request('niche') === $n)>{{ $n }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-3 gap-2">
                    <div>
                        <label class="label">Min followers</label>
                        <input class="input" type="number" name="min_followers" value="{{ request('min_followers') }}" placeholder="10000">
                    </div>
                    <div>
                        <label class="label">Max followers</label>
                        <input class="input" type="number" name="max_followers" value="{{ request('max_followers') }}" placeholder="—">
                    </div>
                    <div>
                        <label class="label">Min ER %</label>
                        <input class="input" type="number" step="0.1" name="min_engagement" value="{{ request('min_engagement') }}" placeholder="3">
                    </div>
                </div>
            </div>

            <div class="mt-4">
                <label class="label">Creator gender</label>
                <div class="flex flex-wrap gap-2">
                    @foreach($genderOpts as $slug => $label)
                        <label class="chip cursor-pointer">
                            <input type="checkbox" name="genders[]" value="{{ $slug }}" class="peer sr-only" @checked(in_array($slug, $genders))>
                            <span class="chip-body">{{ $label }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            <div class="mt-4">
                <label class="label">Creator age range</label>
                <div class="flex flex-wrap gap-2">
                    @foreach($ageOpts as $slug => $label)
                        <label class="chip cursor-pointer">
                            <input type="checkbox" name="ages[]" value="{{ $slug }}" class="peer sr-only" @checked(in_array($slug, $ages))>
                            <span class="chip-body">{{ $label }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            <div class="mt-4">
                <label class="label">Languages</label>
                <div class="flex flex-wrap gap-2">
                    @foreach($langOpts as $slug => $label)
                        <label class="chip cursor-pointer">
                            <input type="checkbox" name="languages[]" value="{{ $slug }}" class="peer sr-only" @checked(in_array($slug, $languages))>
                            <span class="chip-body">{{ $label }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            <div class="mt-4 flex flex-wrap items-center gap-4 text-sm">
                <label class="flex items-center gap-2">
                    <input type="checkbox" name="barter" value="1" @checked(request('barter'))> Accepts barter
                </label>
                <label class="flex items-center gap-2">
                    <input type="checkbox" name="paid" value="1" @checked(request('paid'))> Accepts paid
                </label>
            </div>
        </details>
    </form>

    {{-- Active filter chips --}}
    @php
        $activeChips = [];
        foreach ($cities as $c)    { $activeChips[] = ['label' => '📍 '.($cityOptions[$c] ?? $c), 'key' => 'cities', 'value' => $c]; }
        foreach ($tiers as $t)     { $activeChips[] = ['label' => '⭐ '.($tierOptions[$t]['label'] ?? $t), 'key' => 'tiers', 'value' => $t]; }
        foreach ($genders as $g)   { $activeChips[] = ['label' => ($genderOpts[$g] ?? $g), 'key' => 'genders', 'value' => $g]; }
        foreach ($ages as $a)      { $activeChips[] = ['label' => 'Age '.$a, 'key' => 'ages', 'value' => $a]; }
        foreach ($languages as $l) { $activeChips[] = ['label' => ($langOpts[$l] ?? $l), 'key' => 'languages', 'value' => $l]; }
    @endphp
    @if(count($activeChips))
        <div class="mt-4 flex flex-wrap items-center gap-2">
            <span class="text-xs font-semibold uppercase tracking-wider text-slate-500">Active:</span>
            @foreach($activeChips as $chip)
                <span class="inline-flex items-center gap-1 rounded-full bg-violet-100 px-3 py-1 text-xs text-violet-800">
                    {{ $chip['label'] }}
                </span>
            @endforeach
        </div>
    @endif

    <div class="mt-5 grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
        @forelse($creators as $creator)
            @include('brand.creators._card', ['creator' => $creator, 'activeCampaigns' => $activeCampaigns])
        @empty
            <x-empty-state title="No creators match" icon="🎬" class="sm:col-span-2 lg:col-span-3">
                Try widening your filters — remove a city or add more tiers.
            </x-empty-state>
        @endforelse
    </div>

    <div class="mt-6">{{ $creators->links() }}</div>

    <script>
        // Mode switch (City / Tier) — just toggle visibility of the corresponding picker
        document.querySelectorAll('input[name="mode"]').forEach(r => {
            r.addEventListener('change', e => {
                document.querySelectorAll('[data-panel]').forEach(p => {
                    p.classList.toggle('hidden', p.dataset.panel !== e.target.value);
                });
            });
        });

        // (multi-select and multi-toggle helpers now live globally in resources/js/app.js)
    </script>
</x-layouts.app>
