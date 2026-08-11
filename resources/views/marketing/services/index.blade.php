<x-layouts.app panel="guest"
    title="Influencer marketing services — every service, every Indian city"
    metaDescription="CreatorFlow influencer marketing services — UGC, barter, micro, nano, macro and paid campaigns for Delhi, Mumbai, Bangalore, Hyderabad, Pune and all of India."
    :canonical="route('services.index')">

    @php
        $breadcrumbLd = [
            '@context' => 'https://schema.org',
            '@type'    => 'BreadcrumbList',
            'itemListElement' => [
                ['@type' => 'ListItem', 'position' => 1, 'name' => 'CreatorFlow', 'item' => url('/')],
                ['@type' => 'ListItem', 'position' => 2, 'name' => 'Services',    'item' => route('services.index')],
            ],
        ];
        $collectionLd = [
            '@context' => 'https://schema.org',
            '@type'    => 'CollectionPage',
            'name'     => 'CreatorFlow services',
            'url'      => route('services.index'),
            'description' => 'All CreatorFlow influencer marketing services and city landing pages for India.',
            'numberOfItems' => count($services) * count($cities),
        ];
    @endphp
    <x-marketing.json-ld :blocks="[$breadcrumbLd, $collectionLd]" />

    {{-- Hero --}}
    <section class="relative overflow-hidden">
        <div class="aurora"></div>
        <div class="mx-auto max-w-6xl px-4 pb-10 pt-14 md:pt-20">
            <nav class="text-xs text-slate-500"><a href="{{ url('/') }}" class="hover:text-slate-900">Home</a> → <span>Services</span></nav>
            <div class="mt-3 grid gap-8 md:grid-cols-12 md:items-end">
                <div class="md:col-span-8">
                    <span class="chip"><span class="chip-dot"></span> Services · SEO index</span>
                    <h1 class="mt-4 text-4xl font-black tracking-tight text-slate-900 sm:text-5xl md:text-6xl">
                        Every <span class="text-gradient">influencer marketing</span> service, every Indian city
                    </h1>
                    <p class="mt-4 max-w-2xl text-lg text-slate-600">
                        {{ count($services) }} services × {{ count($cities) }} cities = <strong>{{ count($services) * count($cities) }} landing pages</strong>. Find the exact page that matches what your brand needs — service + location.
                    </p>
                </div>
                <div class="md:col-span-4">
                    <div class="grid grid-cols-3 gap-3 text-center">
                        <div class="rounded-2xl bg-gradient-to-br from-violet-500 to-pink-500 p-4 text-white shadow-lg">
                            <p class="text-2xl font-black">{{ count($services) }}</p>
                            <p class="text-[10px] font-semibold uppercase tracking-wider opacity-90">Services</p>
                        </div>
                        <div class="rounded-2xl bg-gradient-to-br from-cyan-500 to-emerald-500 p-4 text-white shadow-lg">
                            <p class="text-2xl font-black">{{ count($cities) }}</p>
                            <p class="text-[10px] font-semibold uppercase tracking-wider opacity-90">Cities</p>
                        </div>
                        <div class="rounded-2xl bg-gradient-to-br from-amber-500 to-rose-500 p-4 text-white shadow-lg">
                            <p class="text-2xl font-black">{{ count($services) * count($cities) }}</p>
                            <p class="text-[10px] font-semibold uppercase tracking-wider opacity-90">Pages</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Services grid --}}
    <section class="mx-auto max-w-6xl px-4 pb-16 pt-8">
        <div class="mb-6 flex items-end justify-between gap-4">
            <div>
                <p class="section-eyebrow">Browse by service</p>
                <h2 class="mt-2 text-2xl font-black text-slate-900 sm:text-3xl">Pick the exact playbook</h2>
            </div>
            <a href="#by-city" class="hidden text-sm font-semibold text-violet-700 hover:underline sm:inline">Jump to cities ↓</a>
        </div>

        <div class="grid gap-5 md:grid-cols-2 lg:grid-cols-3">
            @foreach($services as $slug => $s)
                <div class="reveal card card-hover group relative flex flex-col overflow-hidden p-6">
                    <div class="absolute -right-8 -top-8 h-28 w-28 rounded-full bg-gradient-to-br {{ $s['grad'] }} opacity-25 blur-2xl transition-transform duration-500 group-hover:scale-125"></div>
                    <div class="relative flex-1">
                        <div class="flex items-start justify-between">
                            <span class="grid h-12 w-12 place-items-center rounded-xl bg-gradient-to-br {{ $s['grad'] }} text-xl text-white shadow-sm">{{ $s['emoji'] }}</span>
                            <a href="{{ route('services.show', $slug) }}" class="text-xs font-semibold text-violet-700 hover:underline">Open →</a>
                        </div>
                        <h3 class="mt-4 text-lg font-black tracking-tight text-slate-900">
                            <a href="{{ route('services.show', $slug) }}" class="hover:underline">{{ $s['name'] }}</a>
                        </h3>
                        <p class="mt-2 text-sm text-slate-600">{{ $s['tagline'] }}</p>
                    </div>
                    <div class="relative mt-4 border-t border-slate-100 pt-3">
                        <p class="text-[10px] font-semibold uppercase tracking-widest text-slate-400">In your city</p>
                        <div class="mt-2 flex flex-wrap gap-1">
                            @foreach(['delhi','mumbai','bangalore','gurugram','india'] as $c)
                                <a href="{{ route('services.city', [$slug, $c]) }}"
                                   class="rounded-full border border-slate-200 bg-white px-2.5 py-1 text-[11px] font-semibold text-slate-600 hover:border-violet-300 hover:bg-violet-50 hover:text-violet-700">
                                    {{ $cities[$c]['name'] }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    {{-- Cities grid --}}
    <section id="by-city" class="mx-auto max-w-6xl px-4 pb-16">
        <div class="mb-6">
            <p class="section-eyebrow">Browse by city</p>
            <h2 class="mt-2 text-2xl font-black text-slate-900 sm:text-3xl">City-specific landing pages</h2>
            <p class="mt-2 max-w-2xl text-sm text-slate-600">Local creators, city-specific rate benchmarks and playbooks — for every top Indian metro.</p>
        </div>

        <div class="grid gap-3 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4">
            @foreach($cities as $slug => $c)
                <div class="reveal card p-5 flex flex-col">
                    <div class="flex items-start justify-between">
                        <div>
                            <h3 class="text-base font-black text-slate-900">{{ $c['name'] }}</h3>
                            <p class="text-xs text-slate-500">{{ $c['region'] }} · {{ $c['pop'] }}</p>
                        </div>
                        <span class="grid h-8 w-8 shrink-0 place-items-center rounded-lg bg-gradient-to-br from-violet-500 to-pink-500 text-white text-sm shadow-sm">📍</span>
                    </div>
                    <div class="mt-3 flex flex-wrap gap-1">
                        @foreach(['influencer-marketing-agency','ugc-influencers','barter-influencers','micro-influencer-marketing'] as $svc)
                            <a href="{{ route('services.city', [$svc, $slug]) }}"
                               class="rounded-full border border-slate-200 bg-white px-2.5 py-1 text-[11px] font-semibold text-slate-600 hover:border-violet-300 hover:bg-violet-50 hover:text-violet-700">
                                {{ $services[$svc]['name'] }}
                            </a>
                        @endforeach
                    </div>
                    <a href="{{ route('services.city', ['influencer-marketing-agency', $slug]) }}" class="mt-auto pt-3 text-xs font-semibold text-violet-700 hover:underline">See all services in {{ $c['name'] }} →</a>
                </div>
            @endforeach
        </div>
    </section>

    {{-- All 121 pages as an SEO sitemap block --}}
    <section class="mx-auto max-w-6xl px-4 pb-20">
        <div class="rounded-3xl border border-slate-200 bg-white p-6 md:p-10">
            <p class="section-eyebrow">Sitemap · {{ count($services) * count($cities) }} landing pages</p>
            <h2 class="mt-2 text-2xl font-black text-slate-900 sm:text-3xl">Every service × every city</h2>
            <p class="mt-2 text-sm text-slate-600">Deep-link to the exact combination your brand cares about.</p>

            <div class="mt-6 space-y-6">
                @foreach($services as $sSlug => $s)
                    <div>
                        <div class="mb-2 flex items-center gap-2">
                            <span class="grid h-7 w-7 place-items-center rounded-lg bg-gradient-to-br {{ $s['grad'] }} text-xs text-white shadow-sm">{{ $s['emoji'] }}</span>
                            <h3 class="font-black text-slate-900">
                                <a href="{{ route('services.show', $sSlug) }}" class="hover:underline">{{ $s['name'] }}</a>
                            </h3>
                            <span class="text-[10px] font-semibold uppercase tracking-widest text-slate-400">· {{ count($cities) }} city pages</span>
                        </div>
                        <div class="flex flex-wrap gap-1.5 text-xs">
                            @foreach($cities as $cSlug => $c)
                                <a href="{{ route('services.city', [$sSlug, $cSlug]) }}"
                                   class="rounded-full border border-slate-200 bg-white px-2.5 py-1 font-semibold text-slate-600 hover:border-violet-300 hover:bg-violet-50 hover:text-violet-700">
                                    {{ $c['name'] }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    @include('marketing._cta')
</x-layouts.app>
