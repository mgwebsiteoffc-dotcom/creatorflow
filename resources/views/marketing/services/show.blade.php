@php
    $canonical  = route('services.show', $slug);
    $title      = "{$service['name']} in India · CreatorPlex";
    $desc       = "{$service['name']}: {$service['tagline']} 1,000+ brands trust CreatorPlex across India for {$service['short']}.";
    $breadcrumbLd = [
        '@context' => 'https://schema.org', '@type' => 'BreadcrumbList',
        'itemListElement' => [
            ['@type' => 'ListItem', 'position' => 1, 'name' => 'Home',     'item' => url('/')],
            ['@type' => 'ListItem', 'position' => 2, 'name' => 'Services', 'item' => route('services.index')],
            ['@type' => 'ListItem', 'position' => 3, 'name' => $service['name'], 'item' => $canonical],
        ],
    ];
    $serviceLd = [
        '@context' => 'https://schema.org', '@type' => 'Service',
        'name'      => $service['name'],
        'serviceType' => $service['short'],
        'url'       => $canonical,
        'description' => $desc,
        'provider'  => ['@type' => 'Organization', 'name' => 'CreatorPlex', 'url' => url('/')],
        'areaServed' => ['@type' => 'Country', 'name' => 'India'],
        'aggregateRating' => ['@type' => 'AggregateRating', 'ratingValue' => '4.9', 'reviewCount' => '128'],
    ];
    $faqLd = [
        '@context' => 'https://schema.org', '@type' => 'FAQPage',
        'mainEntity' => collect($faqs)->map(fn ($q) => [
            '@type' => 'Question', 'name' => $q['q'],
            'acceptedAnswer' => ['@type' => 'Answer', 'text' => $q['a']],
        ])->all(),
    ];
@endphp

<x-layouts.app panel="guest" :title="$title" :metaDescription="$desc" :canonical="$canonical">
    <x-marketing.json-ld :blocks="[$breadcrumbLd, $serviceLd, $faqLd]" />

    <section class="relative overflow-hidden">
        <div class="aurora"></div>
        <div class="dotted absolute inset-0 -z-10"></div>

        <div class="mx-auto max-w-6xl px-4 pb-14 pt-12 md:pt-20">
            <x-marketing.breadcrumbs :crumbs="[
                ['name' => 'Home', 'url' => url('/')],
                ['name' => 'Services', 'url' => route('services.index')],
                ['name' => $service['name'], 'url' => $canonical],
            ]" />

            <div class="mt-6 grid gap-10 md:grid-cols-2 md:items-center md:gap-14">
                <div>
                    <span class="reveal chip"><span class="chip-dot"></span> Service · India</span>
                    <h1 class="reveal mt-5 text-4xl font-black leading-[1.05] tracking-tight text-slate-900 sm:text-5xl md:text-6xl">
                        {{ $service['name'] }} <span class="text-gradient">that ships ROI</span>
                    </h1>
                    <p class="reveal mt-5 max-w-lg text-lg text-slate-600">{{ $service['tagline'] }}</p>

                    <div class="reveal mt-8 grid max-w-md grid-cols-3 gap-3">
                        @foreach($service['kpi'] as $k)
                            <div class="rounded-2xl border border-slate-200 bg-white p-3 text-center shadow-sm">
                                <div class="text-lg font-black text-slate-900">{{ $k[0] }}</div>
                                <div class="mt-0.5 text-[10px] font-semibold uppercase tracking-widest text-slate-500">{{ $k[1] }}</div>
                            </div>
                        @endforeach
                    </div>

                    <div class="reveal mt-8 flex flex-wrap gap-3">
                        <a href="{{ route('register') }}" class="btn-gradient">Start free</a>
                        <a href="#lead-form" class="btn-glass">Talk to sales</a>
                    </div>
                </div>

                <div class="reveal">
                    <div class="relative">
                        <div class="absolute -inset-6 -z-10 rounded-[2rem] blur-2xl" style="background-image: linear-gradient(120deg, {{ $service['accent'] }}55, #7c3aed33);"></div>
                        <div class="g-border p-1 shadow-[0_30px_80px_-30px_rgba(15,23,42,.25)]">
                            <div class="rounded-[calc(1.25rem-1px)] bg-white p-6">
                                <div class="grid h-20 w-20 place-items-center rounded-2xl bg-gradient-to-br {{ $service['grad'] }} text-4xl text-white shadow-md">{{ $service['emoji'] }}</div>
                                <div class="mt-4 text-xs font-semibold uppercase tracking-widest text-slate-500">Service</div>
                                <div class="mt-1 text-xl font-black text-slate-900">{{ $service['name'] }}</div>
                                <p class="mt-3 text-sm text-slate-600">{{ $service['intent'] }}</p>
                                <div class="mt-4 space-y-1.5 text-xs">
                                    @foreach($service['good_for'] as $g)
                                        <div class="flex items-start gap-2 rounded-lg bg-slate-50 p-2">
                                            <span class="mt-0.5 grid h-4 w-4 shrink-0 place-items-center rounded-full bg-gradient-to-br {{ $service['grad'] }} text-[9px] font-black text-white">✓</span>
                                            <span class="text-slate-700">{{ $g }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- CITY GRID --}}
    <section class="mx-auto max-w-6xl px-4 py-16">
        <div class="mx-auto max-w-2xl text-center">
            <p class="section-eyebrow reveal">By city</p>
            <h2 class="section-title reveal mt-3">{{ $service['name'] }} · pick your city</h2>
            <p class="section-sub reveal mt-3">Dedicated pages for {{ count($cities) - 1 }} cities and one national view — local creators, city-specific playbooks.</p>
        </div>

        <div class="mt-10 grid gap-3 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4">
            @foreach($cities as $citySlug => $c)
                <a href="{{ route('services.city', [$slug, $citySlug]) }}"
                   class="reveal card card-hover group flex items-start gap-3 p-4">
                    <div class="grid h-10 w-10 shrink-0 place-items-center rounded-xl bg-gradient-to-br {{ $service['grad'] }} text-white shadow-sm">📍</div>
                    <div class="min-w-0">
                        <div class="truncate font-bold text-slate-900">{{ $service['name'] }} in {{ $c['name'] }}</div>
                        <div class="text-[11px] text-slate-500">{{ $c['region'] }} · {{ $c['pop'] }}</div>
                    </div>
                </a>
            @endforeach
        </div>
    </section>

    {{-- FAQ --}}
    <section class="mx-auto max-w-3xl px-4 py-16">
        <div class="text-center">
            <p class="section-eyebrow reveal">FAQ</p>
            <h2 class="section-title reveal mt-3">Common questions about {{ $service['short'] }}</h2>
        </div>
        <div class="reveal mt-8 divide-y divide-slate-200 rounded-2xl border border-slate-200 bg-white">
            @foreach($faqs as $qa)
                <details class="group p-5" @if($loop->first) open @endif>
                    <summary class="flex cursor-pointer items-center justify-between gap-3 font-semibold text-slate-900">
                        {{ $qa['q'] }}
                        <svg class="h-5 w-5 shrink-0 text-slate-400 transition group-open:rotate-180" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" d="M6 9l6 6 6-6"/></svg>
                    </summary>
                    <p class="mt-3 text-sm leading-relaxed text-slate-600">{{ $qa['a'] }}</p>
                </details>
            @endforeach
        </div>
    </section>

    @include('marketing._lead-form', [
        'source' => 'service:'.$slug,
        'title'  => "Launch a {$service['short']} campaign this week.",
        'sub'    => 'Free 15-minute call — we scope your first campaign, no pressure.',
    ])

    {{-- Related services --}}
    <section class="mx-auto max-w-6xl px-4 pb-20">
        <div class="mx-auto max-w-2xl text-center">
            <p class="section-eyebrow reveal">Related services</p>
            <h2 class="section-title reveal mt-3">More ways to work with us</h2>
        </div>
        <div class="mt-10 grid gap-5 md:grid-cols-2 lg:grid-cols-4">
            @foreach($service['related_service_slugs'] as $rs)
                @php $r = \App\Support\SeoData::service($rs); @endphp
                @if($r)
                    <a href="{{ route('services.show', $rs) }}" class="reveal card card-hover flex items-start gap-3 p-5">
                        <div class="grid h-11 w-11 shrink-0 place-items-center rounded-xl bg-gradient-to-br {{ $r['grad'] }} text-lg text-white shadow-sm">{{ $r['emoji'] }}</div>
                        <div class="min-w-0">
                            <h3 class="truncate font-bold text-slate-900">{{ $r['name'] }}</h3>
                            <p class="mt-1 text-xs text-slate-500">{{ $r['tagline'] }}</p>
                        </div>
                    </a>
                @endif
            @endforeach
        </div>
    </section>

    @include('marketing._cta')
</x-layouts.app>
