<x-layouts.app panel="guest"
    title="Influencer marketing services for Indian DTC brands — CreatorPlex"
    metaDescription="AI-matched creator campaigns for India: UGC, barter, micro/nano influencer marketing, paid Reels, Shopify seeding. Escrow · RazorpayX payouts · real attribution."
    :canonical="route('services.index')">

    @php
        // Map every service slug to a monoline icon in the CreatorPlex library.
        // Kept out of SeoData so we don't polute the SEO / GEO model.
        $serviceIcons = [
            'influencer-marketing-agency'   => 'megaphone',
            'ugc-influencers'               => 'video',
            'barter-influencers'            => 'gift',
            'micro-influencer-marketing'    => 'target',
            'nano-influencer-marketing'     => 'sparkles',
            'creator-marketplace'           => 'creators',
            'paid-influencer-campaigns'     => 'rupee',
            'shopify-influencer-marketing'  => 'shopping-bag',
            'creator-seeding'               => 'package',
            'creator-videoshoot'            => 'video',
            'instagram-influencer-marketing'=> 'image',
        ];

        $breadcrumbLd = [
            '@context' => 'https://schema.org',
            '@type'    => 'BreadcrumbList',
            'itemListElement' => [
                ['@type' => 'ListItem', 'position' => 1, 'name' => 'CreatorPlex', 'item' => url('/')],
                ['@type' => 'ListItem', 'position' => 2, 'name' => 'Services',    'item' => route('services.index')],
            ],
        ];
        $collectionLd = [
            '@context'    => 'https://schema.org',
            '@type'       => 'CollectionPage',
            'name'        => 'CreatorPlex services',
            'url'         => route('services.index'),
            'description' => 'Creator-marketing services for Indian DTC brands: UGC, barter, micro / nano influencer marketing, paid Reels, creator seeding.',
        ];
        // Feature only the top 4 cities on this page — the full 11×11 sitemap
        // now lives at /sitemap (linked in footer) so this page can be about
        // the SERVICES, not a giant deep-link dump.
        $featuredCitySlugs = ['delhi', 'mumbai', 'bangalore', 'gurugram'];
    @endphp

    <x-marketing.json-ld :blocks="[$breadcrumbLd, $collectionLd]" />

    {{-- ===================== HERO ===================== --}}
    <section class="relative overflow-hidden">
        <div class="aurora"></div>
        <div class="mx-auto max-w-6xl px-4 pb-10 pt-16 md:pt-20">
            <nav class="text-xs text-slate-500">
                <a href="{{ url('/') }}" class="hover:text-slate-900">Home</a>
                <span class="px-1 text-slate-300">/</span>
                <span class="text-slate-700">Services</span>
            </nav>

            <div class="mt-6 grid gap-10 md:grid-cols-12 md:items-center">
                <div class="md:col-span-7">
                    <span class="chip"><span class="chip-dot"></span> {{ count($services) }} services · built for India</span>
                    <h1 class="mt-4 text-4xl font-black tracking-tight text-slate-900 sm:text-5xl md:text-[3.4rem] md:leading-[1.05]">
                        Every creator campaign your brand will ever need —
                        <span class="text-gradient">in one platform.</span>
                    </h1>
                    <p class="mt-5 max-w-2xl text-lg text-slate-600">
                        UGC, barter, micro / nano influencer marketing, paid Reels, Shopify seeding, product videoshoots.
                        AI-matched creators, escrow-held budgets, RazorpayX payouts, real Shopify attribution — all on one panel.
                    </p>
                    <div class="mt-7 flex flex-col gap-3 sm:flex-row">
                        <a href="{{ route('register') }}" class="btn-gradient">Start free · 2 campaigns/mo</a>
                        <a href="#services" class="btn-glass">Browse the {{ count($services) }} services</a>
                    </div>
                    <div class="mt-5 flex flex-wrap items-center gap-x-6 gap-y-2 text-xs text-slate-500">
                        <span class="inline-flex items-center gap-1.5"><x-icon name="check" class="h-3.5 w-3.5 text-emerald-600" /> No card required</span>
                        <span class="inline-flex items-center gap-1.5"><x-icon name="check" class="h-3.5 w-3.5 text-emerald-600" /> Cancel anytime</span>
                        <span class="inline-flex items-center gap-1.5"><x-icon name="check" class="h-3.5 w-3.5 text-emerald-600" /> Shopify + web brand panel</span>
                    </div>
                </div>

                {{-- Compact stat card --}}
                <div class="md:col-span-5">
                    <div class="rounded-3xl border border-slate-200 bg-white p-5 shadow-sm">
                        <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Platform snapshot</p>
                        <div class="mt-3 grid grid-cols-2 gap-3">
                            <div class="rounded-2xl bg-gradient-to-br from-violet-500 to-pink-500 p-4 text-white">
                                <p class="text-2xl font-black leading-none">1.05 Cr+</p>
                                <p class="mt-1 text-[10px] font-semibold uppercase tracking-wider opacity-90">Attributed GMV</p>
                            </div>
                            <div class="rounded-2xl bg-gradient-to-br from-emerald-500 to-teal-500 p-4 text-white">
                                <p class="text-2xl font-black leading-none">100K+</p>
                                <p class="mt-1 text-[10px] font-semibold uppercase tracking-wider opacity-90">Verified creators</p>
                            </div>
                            <div class="rounded-2xl bg-gradient-to-br from-amber-500 to-orange-500 p-4 text-white">
                                <p class="text-2xl font-black leading-none">4.8×</p>
                                <p class="mt-1 text-[10px] font-semibold uppercase tracking-wider opacity-90">Avg ROAS</p>
                            </div>
                            <div class="rounded-2xl bg-gradient-to-br from-sky-500 to-indigo-500 p-4 text-white">
                                <p class="text-2xl font-black leading-none">18</p>
                                <p class="mt-1 text-[10px] font-semibold uppercase tracking-wider opacity-90">Indian cities live</p>
                            </div>
                        </div>
                        <p class="mt-3 text-[11px] text-slate-500">All numbers from active brand + creator cohort · updated weekly.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ===================== HOW WE WORK ===================== --}}
    <section class="mx-auto max-w-6xl px-4 py-16">
        <div class="text-center">
            <p class="section-eyebrow">How every campaign runs</p>
            <h2 class="mt-2 text-3xl font-black tracking-tight text-slate-900 sm:text-4xl">Same 5 steps — from brief to attributed ROAS</h2>
            <p class="mx-auto mt-3 max-w-2xl text-sm text-slate-600">Whether it's a ₹0 barter drop or a ₹5L paid campaign, CreatorPlex uses the same loop — so every service is measured the same way.</p>
        </div>

        <div class="mt-10 grid gap-4 md:grid-cols-5">
            @php
                $steps = [
                    ['n' => '1', 'icon' => 'file-text',   'title' => 'Brief',      'body' => 'AI drafts the creative brief from your Shopify catalog · or paste yours.'],
                    ['n' => '2', 'icon' => 'target',      'title' => 'Match',      'body' => '1,000s of Indian creators ranked by niche, tier, city, ER, audience-fit.'],
                    ['n' => '3', 'icon' => 'package',     'title' => 'Ship',       'body' => 'Barter kits + Shopify draft orders · Indian courier presets.'],
                    ['n' => '4', 'icon' => 'check-circle','title' => 'Approve',    'body' => 'Contract e-sign · content review · escrow hold on budget.'],
                    ['n' => '5', 'icon' => 'trend-up',    'title' => 'Attribute',  'body' => 'UTM + coupon + Shopify webhook = live GMV per creator.'],
                ];
            @endphp
            @foreach($steps as $s)
                <div class="relative rounded-2xl border border-slate-200 bg-white p-5">
                    <div class="flex items-center gap-2">
                        <span class="grid h-7 w-7 place-items-center rounded-lg bg-gradient-to-br from-violet-500 to-pink-500 text-xs font-black text-white">{{ $s['n'] }}</span>
                        <x-icon :name="$s['icon']" class="h-4 w-4 text-slate-500" />
                    </div>
                    <h3 class="mt-3 text-sm font-bold text-slate-900">{{ $s['title'] }}</h3>
                    <p class="mt-1 text-xs leading-relaxed text-slate-600">{{ $s['body'] }}</p>
                </div>
            @endforeach
        </div>
    </section>

    {{-- ===================== SERVICES GRID ===================== --}}
    <section id="services" class="mx-auto max-w-6xl px-4 py-16">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <p class="section-eyebrow">Browse by service</p>
                <h2 class="mt-2 text-3xl font-black tracking-tight text-slate-900 sm:text-4xl">Pick the exact playbook</h2>
                <p class="mt-2 max-w-2xl text-sm text-slate-600">Each service is a self-contained module — brief templates, creator filters, deliverable checklist, pricing benchmark.</p>
            </div>
            <a href="#cities" class="text-sm font-semibold text-violet-700 hover:underline">Or browse by city →</a>
        </div>

        <div class="mt-8 grid gap-4 md:grid-cols-2 lg:grid-cols-3">
            @foreach($services as $slug => $s)
                <a href="{{ route('services.show', $slug) }}"
                   class="group relative flex flex-col overflow-hidden rounded-2xl border border-slate-200 bg-white p-5 transition hover:-translate-y-0.5 hover:border-violet-300 hover:shadow-lg">
                    <div class="absolute -right-6 -top-6 h-24 w-24 rounded-full bg-gradient-to-br {{ $s['grad'] }} opacity-20 blur-2xl transition-transform duration-500 group-hover:scale-125"></div>

                    <div class="relative flex items-start justify-between gap-3">
                        <span class="grid h-10 w-10 place-items-center rounded-xl bg-gradient-to-br {{ $s['grad'] }} text-white shadow-sm">
                            <x-icon :name="$serviceIcons[$slug] ?? 'sparkles'" class="h-5 w-5" />
                        </span>
                        <span class="inline-flex items-center gap-1 text-[11px] font-semibold text-slate-500 transition group-hover:text-violet-700">
                            Open <x-icon name="chevron-down" class="h-3 w-3 -rotate-90" />
                        </span>
                    </div>

                    <h3 class="relative mt-4 text-base font-black tracking-tight text-slate-900">{{ $s['name'] }}</h3>
                    <p class="relative mt-1 text-xs leading-relaxed text-slate-600">{{ $s['tagline'] }}</p>

                    {{-- Compact KPI trio pulled from SeoData::services() --}}
                    @if(! empty($s['kpi']))
                        <div class="relative mt-4 grid grid-cols-3 gap-1.5 border-t border-slate-100 pt-3">
                            @foreach(array_slice($s['kpi'], 0, 3) as $k)
                                <div class="min-w-0">
                                    <p class="truncate text-[13px] font-black text-slate-900">{{ $k[0] }}</p>
                                    <p class="mt-0.5 truncate text-[10px] font-semibold uppercase tracking-wider text-slate-400">{{ $k[1] }}</p>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </a>
            @endforeach
        </div>
    </section>

    {{-- ===================== WHY CREATORPLEX ===================== --}}
    <section class="border-y border-slate-200 bg-gradient-to-b from-slate-50 to-white">
        <div class="mx-auto max-w-6xl px-4 py-16">
            <div class="text-center">
                <p class="section-eyebrow">Built India-first</p>
                <h2 class="mt-2 text-3xl font-black tracking-tight text-slate-900 sm:text-4xl">4 things global tools can't do — we do by default</h2>
            </div>

            @php
                $edges = [
                    ['icon' => 'rupee',    'title' => 'INR-native · RazorpayX payouts', 'body' => 'UPI · IFSC · PAN · GST invoicing built in. Creators paid to their UPI in hours, not weeks.'],
                    ['icon' => 'lock',     'title' => 'Escrow + contract e-sign',       'body' => 'Brand funds held on-approval, released after content clears review. Built-in signature pad — no DocuSign fees.'],
                    ['icon' => 'chat',     'title' => 'WhatsApp-first workflow',        'body' => 'Every state change fires an approved WhatsApp template. Creators respond without leaving the app.'],
                    ['icon' => 'trend-up', 'title' => 'Real Shopify attribution',       'body' => 'UTM + coupon + Shopify webhook = live GMV per creator. Not "trust me" ROAS.'],
                ];
            @endphp
            <div class="mt-10 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                @foreach($edges as $e)
                    <div class="rounded-2xl border border-slate-200 bg-white p-5">
                        <span class="grid h-10 w-10 place-items-center rounded-xl bg-slate-900 text-white">
                            <x-icon :name="$e['icon']" class="h-5 w-5" />
                        </span>
                        <h3 class="mt-4 text-sm font-bold text-slate-900">{{ $e['title'] }}</h3>
                        <p class="mt-1 text-xs leading-relaxed text-slate-600">{{ $e['body'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ===================== BY CITY (compact) ===================== --}}
    <section id="cities" class="mx-auto max-w-6xl px-4 py-16">
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div>
                <p class="section-eyebrow">Local creator networks</p>
                <h2 class="mt-2 text-3xl font-black tracking-tight text-slate-900 sm:text-4xl">Creator campaigns in 18+ Indian cities</h2>
                <p class="mt-2 max-w-xl text-sm text-slate-600">Vetted creators in every major Indian metro, with city-specific rate benchmarks + local language coverage.</p>
            </div>
            <a href="#all-cities" class="text-sm font-semibold text-violet-700 hover:underline">See all {{ count($cities) }} cities →</a>
        </div>

        <div class="mt-8 grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
            @foreach($featuredCitySlugs as $slug)
                @php $c = $cities[$slug] ?? null; @endphp
                @continue(! $c)
                <a href="{{ route('services.city', ['influencer-marketing-agency', $slug]) }}"
                   class="group rounded-2xl border border-slate-200 bg-white p-5 transition hover:-translate-y-0.5 hover:border-violet-300 hover:shadow-md">
                    <div class="flex items-start justify-between">
                        <div class="min-w-0">
                            <h3 class="text-base font-black text-slate-900">{{ $c['name'] }}</h3>
                            <p class="mt-0.5 truncate text-xs text-slate-500">{{ $c['region'] }} · {{ $c['pop'] }}</p>
                        </div>
                        <span class="grid h-8 w-8 shrink-0 place-items-center rounded-lg bg-slate-900 text-white">
                            <x-icon name="map-pin" class="h-4 w-4" />
                        </span>
                    </div>
                    <p class="mt-3 line-clamp-2 text-xs leading-relaxed text-slate-600">{{ $c['note'] ?? '' }}</p>
                    <p class="mt-4 inline-flex items-center gap-1 text-xs font-semibold text-violet-700 group-hover:underline">
                        View {{ $c['name'] }} agency page <x-icon name="chevron-down" class="h-3 w-3 -rotate-90" />
                    </p>
                </a>
            @endforeach
        </div>

        {{-- Complete city list as tight pills — no more giant matrix --}}
        <div id="all-cities" class="mt-6 rounded-2xl border border-slate-200 bg-slate-50/70 p-4">
            <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400">All cities live</p>
            <div class="mt-2 flex flex-wrap gap-1.5">
                @foreach($cities as $slug => $c)
                    <a href="{{ route('services.city', ['influencer-marketing-agency', $slug]) }}"
                       class="rounded-md border border-slate-200 bg-white px-2.5 py-1 text-[11px] font-semibold text-slate-600 hover:border-violet-300 hover:text-violet-700">
                        {{ $c['name'] }}
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ===================== TESTIMONIALS (marquee) ===================== --}}
    <section class="border-y border-slate-200 bg-white py-16">
        <div class="mx-auto max-w-6xl px-4">
            <div class="text-center">
                <p class="section-eyebrow">Loved by DTC founders</p>
                <h2 class="mt-2 text-3xl font-black tracking-tight text-slate-900 sm:text-4xl">Real brands, real receipts</h2>
            </div>

            @php
                $quotes = [
                    ['q' => 'Set up in 20 minutes, shipped 40 barter kits the next day. Reels racked up 3.4M reach in a week.', 'name' => 'Samsara Ghee', 'role' => 'Food · D2C', 'grad' => 'from-amber-500 to-orange-500'],
                    ['q' => 'The AI matches were spot-on — we stopped guessing which creator to pick and started shipping.',   'name' => 'Roving Mode',   'role' => 'Fashion brand', 'grad' => 'from-indigo-500 to-violet-500'],
                    ['q' => 'Escrow + auto-payouts to creators changed the trust game. Zero disputes in 3 months.',            'name' => 'Nykaa Sellers', 'role' => 'Beauty · marketplace', 'grad' => 'from-fuchsia-500 to-pink-500'],
                    ['q' => 'Our first paid Reels campaign in Mumbai closed at 6.1× ROAS. Attribution was live.',              'name' => 'Luxotica',      'role' => 'Cosmetic brand', 'grad' => 'from-pink-500 to-rose-500'],
                    ['q' => 'We used to run creators on WhatsApp. Now every deal has a contract + tracking + payment log.',    'name' => 'weRbangali',    'role' => 'Regional fashion', 'grad' => 'from-cyan-500 to-emerald-500'],
                    ['q' => 'Shopify draft orders auto-created the moment a creator accepted. Ops team saved 15 hours/week.', 'name' => 'Fable Street',  'role' => 'Apparel · Shopify', 'grad' => 'from-violet-500 to-purple-500'],
                ];
            @endphp
            <div class="relative mt-10">
                <div class="pointer-events-none absolute inset-y-0 left-0 z-10 w-16 bg-gradient-to-r from-white to-transparent"></div>
                <div class="pointer-events-none absolute inset-y-0 right-0 z-10 w-16 bg-gradient-to-l from-white to-transparent"></div>
                <div class="testimonial-marquee overflow-hidden">
                    <div class="testimonial-track flex w-max gap-6 py-2">
                        @foreach(array_merge($quotes, $quotes) as $q)
                            <figure class="card w-[300px] shrink-0 p-5 md:w-[340px]">
                                <div class="flex items-center gap-0.5 text-amber-500">
                                    @for($i=0;$i<5;$i++)★@endfor
                                </div>
                                <blockquote class="mt-3 text-sm text-slate-700">&ldquo;{{ $q['q'] }}&rdquo;</blockquote>
                                <figcaption class="mt-4 flex items-center gap-3">
                                    <span class="grid h-9 w-9 place-items-center rounded-full bg-gradient-to-br {{ $q['grad'] }} text-white font-bold">{{ substr($q['name'],0,1) }}</span>
                                    <div>
                                        <div class="text-sm font-bold text-slate-900">{{ $q['name'] }}</div>
                                        <div class="text-xs text-slate-500">{{ $q['role'] }}</div>
                                    </div>
                                </figcaption>
                            </figure>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ===================== PRICING TEASER ===================== --}}
    <section class="mx-auto max-w-6xl px-4 py-16">
        <div class="grid gap-4 md:grid-cols-3">
            @php
                $tiers = [
                    ['label' => 'Free',   'price' => '₹0',       'sub' => '2 campaigns / mo · barter only', 'cta' => 'Start free', 'href' => route('register'), 'style' => 'border-slate-200 bg-white'],
                    ['label' => 'Growth', 'price' => '₹2,499',   'sub' => 'Unlimited barter · paid + escrow · analytics', 'cta' => 'Try Growth', 'href' => route('register'), 'style' => 'border-violet-300 bg-gradient-to-br from-violet-50 to-pink-50 ring-1 ring-violet-200'],
                    ['label' => 'Scale',  'price' => '₹12,999',  'sub' => 'White-label agency mode · priority vetting · API', 'cta' => 'Talk to sales', 'href' => url('/contact'), 'style' => 'border-slate-200 bg-white'],
                ];
            @endphp
            @foreach($tiers as $t)
                <div class="rounded-2xl border {{ $t['style'] }} p-6">
                    <p class="text-xs font-bold uppercase tracking-widest text-slate-500">{{ $t['label'] }}</p>
                    <p class="mt-2 text-3xl font-black text-slate-900">{{ $t['price'] }}<span class="text-sm font-semibold text-slate-500">/mo</span></p>
                    <p class="mt-2 text-xs leading-relaxed text-slate-600">{{ $t['sub'] }}</p>
                    <a href="{{ $t['href'] }}" class="btn-primary mt-5 w-full">{{ $t['cta'] }}</a>
                </div>
            @endforeach
        </div>
        <p class="mt-4 text-center text-xs text-slate-500">
            Full pricing breakdown on <a href="{{ url('/pricing') }}" class="font-semibold text-violet-700 hover:underline">the pricing page</a>.
        </p>
    </section>

    @include('marketing._cta')
</x-layouts.app>
