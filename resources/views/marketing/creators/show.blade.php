@php
    $tier = $creator->currentTier();
    $tierLabel = $tier ? \App\Support\CreatorTaxonomy::tiers()[$tier]['label'] : null;
@endphp
<x-layouts.app panel="guest"
    :title="$creator->display_name.' — '.($creator->city ? $creator->city.' ' : '').'creator on CreatorPlex'"
    :metaDescription="\Illuminate\Support\Str::limit(($creator->display_name.' · '.number_format($creator->follower_count_total).' followers · '.$creator->engagement_rate.'% engagement rate · '.($creator->bio ?? '')), 155)">

    @php
        $ld = [
            '@context' => 'https://schema.org', '@type' => 'ProfilePage',
            'mainEntity' => [
                '@type' => 'Person',
                'name' => $creator->display_name,
                'description' => $creator->bio,
                'address' => $creator->city ? ['@type' => 'PostalAddress', 'addressLocality' => $creator->city, 'addressCountry' => 'IN'] : null,
            ],
        ];
    @endphp
    <x-marketing.json-ld :blocks="[$ld]" />

    {{-- HERO --}}
    <section class="relative overflow-hidden bg-gradient-to-br from-violet-600 via-pink-500 to-amber-500 pb-24 pt-16 text-white md:pt-20">
        <div class="mx-auto max-w-5xl px-4">
            <nav class="text-xs text-white/80"><a href="{{ route('creators.index') }}" class="hover:text-white">Creators</a> → <span>{{ $creator->display_name }}</span></nav>
            <div class="mt-6 flex flex-wrap items-center gap-5">
                <div class="grid h-24 w-24 place-items-center rounded-2xl border-4 border-white bg-white text-3xl font-black text-violet-700 shadow-xl">{{ strtoupper(substr($creator->display_name, 0, 2)) }}</div>
                <div class="min-w-0 flex-1">
                    <h1 class="text-3xl font-black tracking-tight sm:text-5xl">{{ $creator->display_name }}</h1>
                    <p class="mt-2 text-sm text-white/80">{{ $creator->city ? '📍 '.$creator->city.' · India · ' : '' }}{{ $creator->languages ? implode(', ', (array) $creator->languages) : '' }}</p>
                    <div class="mt-4 flex flex-wrap gap-2 text-xs">
                        @if($tierLabel)<span class="rounded-full bg-white/25 px-3 py-1 font-bold uppercase backdrop-blur">{{ $tierLabel }}</span>@endif
                        @foreach($creator->nicheRows->take(4) as $n)
                            <span class="rounded-full bg-white/15 px-3 py-1 backdrop-blur">{{ $n->niche }}</span>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- KPI --}}
    <section class="-mt-14 mx-auto max-w-5xl px-4">
        <div class="grid gap-3 rounded-3xl border border-slate-200 bg-white p-4 shadow-lg sm:grid-cols-4">
            <div class="rounded-2xl bg-gradient-to-br from-violet-500 to-pink-500 p-4 text-white"><p class="text-[10px] font-bold uppercase tracking-widest opacity-90">Followers</p><p class="mt-1 text-2xl font-black">{{ number_format($creator->follower_count_total) }}</p></div>
            <div class="rounded-2xl bg-gradient-to-br from-cyan-500 to-emerald-500 p-4 text-white"><p class="text-[10px] font-bold uppercase tracking-widest opacity-90">Engagement</p><p class="mt-1 text-2xl font-black">{{ $creator->engagement_rate }}%</p></div>
            <div class="rounded-2xl bg-gradient-to-br from-amber-500 to-rose-500 p-4 text-white"><p class="text-[10px] font-bold uppercase tracking-widest opacity-90">Avg views</p><p class="mt-1 text-2xl font-black">{{ number_format($creator->avg_views) }}</p></div>
            <div class="rounded-2xl bg-gradient-to-br from-slate-700 to-slate-900 p-4 text-white"><p class="text-[10px] font-bold uppercase tracking-widest opacity-90">Performance</p><p class="mt-1 text-2xl font-black">{{ $creator->performance_score }}</p></div>
        </div>
    </section>

    <section class="mx-auto max-w-5xl px-4 py-12 grid gap-8 md:grid-cols-3">
        <div class="md:col-span-2 space-y-6">
            @if($creator->bio)
                <div class="rounded-2xl border border-slate-200 bg-white p-6">
                    <h2 class="text-lg font-black text-slate-900">About</h2>
                    <p class="mt-2 whitespace-pre-line text-sm text-slate-700 leading-relaxed">{{ $creator->bio }}</p>
                </div>
            @endif

            @if($creator->portfolioItems->isNotEmpty())
                <div class="rounded-2xl border border-slate-200 bg-white p-6">
                    <h2 class="text-lg font-black text-slate-900">🎬 Portfolio</h2>
                    <div class="mt-4 grid grid-cols-2 gap-3 sm:grid-cols-3">
                        @foreach($creator->portfolioItems as $p)
                            <a @if($p->external_url) href="{{ $p->external_url }}" target="_blank" rel="noopener" @endif class="group aspect-square overflow-hidden rounded-xl bg-slate-100">
                                @if($p->thumbnail_path || $p->path)
                                    <img src="{{ $p->thumbnail_path ?: $p->path }}" alt="{{ $p->title }}" class="h-full w-full object-cover transition group-hover:scale-105">
                                @else
                                    <div class="grid h-full w-full place-items-center text-slate-400">🎬</div>
                                @endif
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <aside class="space-y-6">
            @if($creator->socialAccounts->isNotEmpty())
                <div class="rounded-2xl border border-slate-200 bg-white p-6">
                    <h2 class="text-lg font-black text-slate-900">Social</h2>
                    <div class="mt-4 space-y-2">
                        @foreach($creator->socialAccounts as $s)
                            <a href="{{ $s->url ?: '#' }}" target="_blank" rel="noopener" class="flex items-center justify-between rounded-xl border border-slate-100 p-3 hover:border-violet-300">
                                <div>
                                    <p class="text-sm font-semibold text-slate-900 capitalize">{{ $s->platform }} · @{{ $s->handle }}</p>
                                    <p class="text-xs text-slate-500">{{ number_format($s->follower_count) }} followers · {{ $s->engagement_rate }}% ER</p>
                                </div>
                                <span class="text-slate-300">↗</span>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            <div class="rounded-2xl border border-slate-200 bg-gradient-to-br from-slate-900 to-slate-950 p-6 text-white">
                <h2 class="text-lg font-black">Work with {{ $creator->display_name }}</h2>
                <p class="mt-1 text-xs text-white/70">Brands invite creators through CreatorPlex campaigns — with contracts, unique discount codes, and revenue attribution baked in.</p>
                <a href="{{ route('register') }}" class="btn-gradient mt-4 w-full !py-2 text-xs text-center">Invite to campaign →</a>
                <a href="{{ route('creators.index') }}" class="btn-glass mt-2 w-full !py-2 text-xs text-center">Browse more creators</a>
            </div>
        </aside>
    </section>
</x-layouts.app>
