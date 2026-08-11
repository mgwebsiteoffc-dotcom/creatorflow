<x-layouts.app panel="guest"
    title="Influencer marketing services"
    metaDescription="Every CreatorFlow service and city page. Influencer marketing agency, UGC influencers, barter campaigns, creator seeding — India-wide with dedicated city landing pages."
    :canonical="route('services.index')">

    <x-marketing.json-ld :blocks="[[
        '@context' => 'https://schema.org',
        '@type'    => 'CollectionPage',
        'name'     => 'CreatorFlow services',
        'url'      => route('services.index'),
        'description' => 'All CreatorFlow influencer marketing services and cities.',
    ]]" />

    @include('marketing._hero', [
        'eyebrow' => 'Services · SEO index',
        'title'   => 'Every <span class="text-gradient">influencer marketing</span> service, every city',
        'sub'     => 'Programmatic pages so brands find us by exactly what they need — service + location.',
        'ctaText' => 'Start free',
    ])

    <section class="mx-auto max-w-6xl px-4 pb-16">
        <div class="mx-auto max-w-2xl text-center">
            <p class="section-eyebrow reveal">Browse by service</p>
            <h2 class="section-title reveal mt-3">{{ count($services) }} services · {{ count($cities) }} cities · {{ count($services) * count($cities) }} landing pages</h2>
        </div>

        <div class="mt-10 grid gap-5 md:grid-cols-2 lg:grid-cols-3">
            @foreach($services as $slug => $s)
                <a href="{{ route('services.show', $slug) }}" class="reveal card card-hover group relative flex flex-col overflow-hidden p-6">
                    <div class="absolute -right-8 -top-8 h-28 w-28 rounded-full bg-gradient-to-br {{ $s['grad'] }} opacity-25 blur-2xl transition-transform duration-500 group-hover:scale-125"></div>
                    <div class="relative">
                        <span class="grid h-11 w-11 place-items-center rounded-xl bg-gradient-to-br {{ $s['grad'] }} text-lg text-white shadow-sm">{{ $s['emoji'] }}</span>
                        <h3 class="mt-4 text-lg font-bold text-slate-900">{{ $s['name'] }}</h3>
                        <p class="mt-2 text-sm text-slate-600">{{ $s['tagline'] }}</p>
                        <div class="mt-4 flex flex-wrap gap-1">
                            @foreach(['delhi','mumbai','bangalore','india'] as $c)
                                <a href="{{ route('services.city', [$slug, $c]) }}" class="rounded-full border border-slate-200 bg-white px-2 py-0.5 text-[10px] font-semibold text-slate-600 hover:border-violet-300 hover:text-violet-700">{{ $cities[$c]['name'] }}</a>
                            @endforeach
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    </section>

    <section class="mx-auto max-w-6xl px-4 pb-16">
        <div class="mx-auto max-w-2xl text-center">
            <p class="section-eyebrow reveal">Browse by city</p>
            <h2 class="section-title reveal mt-3">City-specific landing pages</h2>
            <p class="section-sub reveal mt-3">Local creators, city-specific playbooks, and India-wide coverage.</p>
        </div>

        <div class="mt-10 grid gap-3 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4">
            @foreach($cities as $slug => $c)
                <div class="reveal card p-5">
                    <h3 class="text-base font-bold text-slate-900">{{ $c['name'] }} <span class="text-xs font-normal text-slate-500">· {{ $c['region'] }}</span></h3>
                    <p class="mt-1 text-xs text-slate-500">{{ $c['pop'] }}</p>
                    <div class="mt-3 flex flex-wrap gap-1">
                        @foreach(['influencer-marketing-agency','ugc-influencers','barter-influencers'] as $svc)
                            <a href="{{ route('services.city', [$svc, $slug]) }}" class="rounded-full border border-slate-200 bg-white px-2 py-0.5 text-[10px] font-semibold text-slate-600 hover:border-violet-300 hover:text-violet-700">{{ $services[$svc]['name'] }}</a>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    @include('marketing._cta')
</x-layouts.app>
