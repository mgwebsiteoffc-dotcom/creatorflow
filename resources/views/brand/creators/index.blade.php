<x-layouts.app panel="brand" title="Creator marketplace">
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black tracking-tight text-slate-900 md:text-3xl">Creator marketplace</h1>
            <p class="mt-1 text-sm text-slate-500">Search verified Indian creators. Filter by city, tier, niche &amp; audience — then invite to a campaign.</p>
        </div>
        <div class="text-xs font-semibold text-slate-500">
            <span class="inline-flex items-center gap-1.5"><span class="h-2 w-2 rounded-full bg-emerald-500"></span> {{ number_format($creators->total()) }} match{{ $creators->total() === 1 ? '' : 'es' }}</span>
        </div>
    </div>

    {{-- ============================ FILTER BAR ============================ --}}
    <form method="GET" class="card mt-5 p-4"
          data-skeleton-target="#creators-list"
          data-skeleton-slot="#creators-list-skeleton">
        {{-- Row 1: primary controls — always visible --}}
        <div class="grid gap-2 md:grid-cols-12">
            {{-- Search --}}
            <div class="md:col-span-4">
                <label class="label">Search</label>
                <div class="relative">
                    <x-icon name="search" class="pointer-events-none absolute left-2.5 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" />
                    <input type="search" name="q" value="{{ request('q') }}" placeholder="Name, handle, bio…"
                           class="input !pl-8">
                </div>
            </div>

            {{-- Cities dropdown --}}
            <div class="md:col-span-4">
                <label class="label">Cities</label>
                <x-multi-select
                    name="cities[]"
                    :options="$cityOptions"
                    :selected="$cities"
                    placeholder="Any city"
                    search-placeholder="Search cities…" />
            </div>

            {{-- Tier dropdown --}}
            <div class="md:col-span-4">
                <label class="label">Follower tier</label>
                @php
                    $tierPairs = collect($tierOptions)->mapWithKeys(fn ($t, $slug) => [$slug => $t['label'].' · '.$t['range']])->all();
                @endphp
                <x-multi-select
                    name="tiers[]"
                    :options="$tierPairs"
                    :selected="$tiers"
                    placeholder="Any tier"
                    search-placeholder="Search tiers…" />
            </div>

            {{-- Niche + engagement + apply --}}
            <div class="md:col-span-4">
                <label class="label">Niche</label>
                <select class="input" name="niche">
                    <option value="">Any niche</option>
                    @foreach($niches as $n)
                        <option value="{{ $n }}" @selected(request('niche') === $n)>{{ $n }}</option>
                    @endforeach
                </select>
            </div>
            <div class="md:col-span-2">
                <label class="label">Min followers</label>
                <input class="input" type="number" name="min_followers" value="{{ request('min_followers') }}" placeholder="10,000">
            </div>
            <div class="md:col-span-2">
                <label class="label">Max followers</label>
                <input class="input" type="number" name="max_followers" value="{{ request('max_followers') }}" placeholder="No limit">
            </div>
            <div class="md:col-span-2">
                <label class="label">Min ER %</label>
                <input class="input" type="number" step="0.1" name="min_engagement" value="{{ request('min_engagement') }}" placeholder="3.0">
            </div>
            <div class="md:col-span-2 flex items-end gap-2">
                <button class="btn-primary flex-1">Apply</button>
                @if(request()->hasAny(['q','cities','tiers','genders','ages','languages','niche','min_followers','max_followers','min_engagement','barter','paid']))
                    <a href="{{ route('brand.creators.index') }}" class="btn-secondary" title="Reset filters">Reset</a>
                @endif
            </div>
        </div>

        {{-- Row 2: extra refiners in a collapsible details panel --}}
        <details class="mt-3 rounded-lg border border-slate-100 bg-slate-50/50">
            <summary class="cursor-pointer select-none px-3 py-2 text-xs font-semibold text-slate-600 hover:text-slate-900">
                Audience refiners — gender, age, language, deal type
            </summary>
            <div class="space-y-3 border-t border-slate-100 p-3">
                <div class="grid gap-3 md:grid-cols-2">
                    <div>
                        <label class="label">Creator gender</label>
                        <div class="flex flex-wrap gap-1.5">
                            @foreach($genderOpts as $slug => $label)
                                <label class="cursor-pointer">
                                    <input type="checkbox" name="genders[]" value="{{ $slug }}" class="peer sr-only" @checked(in_array($slug, $genders))>
                                    <span class="mini-chip">{{ $label }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                    <div>
                        <label class="label">Creator age</label>
                        <div class="flex flex-wrap gap-1.5">
                            @foreach($ageOpts as $slug => $label)
                                <label class="cursor-pointer">
                                    <input type="checkbox" name="ages[]" value="{{ $slug }}" class="peer sr-only" @checked(in_array($slug, $ages))>
                                    <span class="mini-chip tabular-nums">{{ $label }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div>
                    <label class="label">Languages</label>
                    <x-multi-select
                        name="languages[]"
                        :options="$langOpts"
                        :selected="$languages"
                        placeholder="Any language"
                        search-placeholder="Search languages…" />
                </div>

                <div class="flex flex-wrap items-center gap-4 pt-1 text-xs">
                    <label class="flex items-center gap-1.5 font-medium text-slate-600">
                        <input type="checkbox" name="barter" value="1" @checked(request('barter')) class="h-4 w-4 rounded border-slate-300 text-violet-600">
                        Accepts barter
                    </label>
                    <label class="flex items-center gap-1.5 font-medium text-slate-600">
                        <input type="checkbox" name="paid" value="1" @checked(request('paid')) class="h-4 w-4 rounded border-slate-300 text-violet-600">
                        Accepts paid
                    </label>
                </div>
            </div>
        </details>
    </form>

    {{-- Active filter chips --}}
    @php
        $activeChips = [];
        foreach ($cities as $c)    { $activeChips[] = ['label' => ($cityOptions[$c] ?? $c)]; }
        foreach ($tiers as $t)     { $activeChips[] = ['label' => ($tierOptions[$t]['label'] ?? $t)]; }
        foreach ($genders as $g)   { $activeChips[] = ['label' => ($genderOpts[$g] ?? $g)]; }
        foreach ($ages as $a)      { $activeChips[] = ['label' => 'Age '.$a]; }
        foreach ($languages as $l) { $activeChips[] = ['label' => ($langOpts[$l] ?? $l)]; }
    @endphp
    @if(count($activeChips))
        <div class="mt-3 flex flex-wrap items-center gap-1.5 text-xs">
            <span class="font-semibold uppercase tracking-wider text-slate-400">Active:</span>
            @foreach($activeChips as $chip)
                <span class="rounded-md bg-violet-100 px-2 py-0.5 font-semibold text-violet-800">{{ $chip['label'] }}</span>
            @endforeach
        </div>
    @endif

    {{-- ============================ CREATOR CARDS ============================ --}}
    <div id="creators-list" class="mt-5 grid gap-3 sm:grid-cols-2 lg:grid-cols-3 transition-opacity">
        @forelse($creators as $creator)
            @include('brand.creators._card', ['creator' => $creator, 'activeCampaigns' => $activeCampaigns])
        @empty
            <x-empty-state title="No creators match" icon="creators" class="sm:col-span-2 lg:col-span-3">
                Try widening your filters — remove a city, add more tiers, or lower the follower range.
                <x-slot:action><a href="{{ route('brand.creators.index') }}" class="btn-primary">Reset filters</a></x-slot:action>
            </x-empty-state>
        @endforelse
    </div>

    {{-- Skeleton overlay shown while pagination / filter submit is in flight --}}
    <x-skeleton-card id="creators-list-skeleton" class="mt-5 hidden" :count="6" />

    <div class="mt-6" data-skeleton-container="#creators-list">{{ $creators->links() }}</div>
</x-layouts.app>
