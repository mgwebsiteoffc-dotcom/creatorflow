<x-layouts.app panel="guest" title="About">
    @include('marketing._hero', [
        'eyebrow' => 'About CreatorFlow',
        'title'   => 'We\'re building the <span class="text-gradient">creator commerce</span> operating system',
        'sub'     => 'Because running influencer campaigns shouldn\'t feel like duct-taping five tools together. One backend, two doors, endless upside.',
        'ctaText' => 'Join us',
    ])

    <section class="mx-auto max-w-6xl px-4 pb-16">
        <div class="grid gap-6 md:grid-cols-4">
            @foreach([
                ['100K+', 'Verified creators'],
                ['1,000+', 'Active brands'],
                ['$46M+', 'GMV attributed'],
                ['4.9★', 'Avg rating'],
            ] as $stat)
                <div class="reveal card p-6 text-center">
                    <div class="text-3xl font-black text-gradient" style="background-image: linear-gradient(120deg,#7c3aed,#ec4899,#f59e0b);">{{ $stat[0] }}</div>
                    <div class="mt-2 text-xs font-semibold uppercase tracking-widest text-slate-500">{{ $stat[1] }}</div>
                </div>
            @endforeach
        </div>
    </section>

    {{-- Story --}}
    <section class="mx-auto max-w-4xl px-4 py-16">
        <div class="reveal prose prose-slate max-w-none">
            <p class="section-eyebrow">Our story</p>
            <h2 class="section-title mt-3 text-left">Built by operators who lived the pain</h2>
            <p class="mt-6 text-lg text-slate-600">
                Our founding team ran creator campaigns for DTC brands doing 8-figure GMV. Every launch meant spreadsheets, DMs, tag-based attribution guesses, and 3am ops fires. We built CreatorFlow because we wanted the tool we couldn't buy.
            </p>
            <p class="mt-4 text-slate-600">
                Today CreatorFlow powers seeding, UGC, and paid creator campaigns for 1,000+ brands — from Shopify's fastest movers to global retail. Same product, same engine, whether you're doing your first 5-creator drop or seeding 500 at once.
            </p>
        </div>
    </section>

    {{-- Values --}}
    <section class="mx-auto max-w-6xl px-4 py-16">
        <div class="mx-auto max-w-2xl text-center">
            <p class="section-eyebrow reveal">What we believe</p>
            <h2 class="section-title reveal mt-3">Six values, no fluff</h2>
        </div>
        <div class="mt-12 grid gap-5 md:grid-cols-2 lg:grid-cols-3">
            @foreach([
                ['⚡', 'Ship weekly', 'We move fast because the creator economy does.'],
                ['🎯', 'Outcomes over features', 'Every screen exists to move a metric.'],
                ['🤝', 'Creators keep 100%', 'No platform fees. Ever.'],
                ['🧠', 'AI as a copilot', 'Automate the drudge. Keep humans in the loop.'],
                ['🔒', 'Data you can trust', 'Real attribution beats vanity metrics.'],
                ['🌍', 'Global from day one', 'INR, USD, EUR — brands and creators anywhere.'],
            ] as $v)
                <div class="reveal card card-hover p-6">
                    <div class="text-2xl">{{ $v[0] }}</div>
                    <h3 class="mt-3 font-bold text-slate-900">{{ $v[1] }}</h3>
                    <p class="mt-1 text-sm text-slate-600">{{ $v[2] }}</p>
                </div>
            @endforeach
        </div>
    </section>

    {{-- Team --}}
    <section class="mx-auto max-w-6xl px-4 py-16">
        <div class="mx-auto max-w-2xl text-center">
            <p class="section-eyebrow reveal">Team</p>
            <h2 class="section-title reveal mt-3">Small, senior, remote-first</h2>
        </div>
        <div class="mt-12 grid gap-6 sm:grid-cols-2 md:grid-cols-4">
            @foreach([
                ['Priya S.', 'CEO · ex-DTC ops',           'from-violet-500 to-pink-500'],
                ['Marcus C.','CTO · ex-Shopify engineer', 'from-cyan-500 to-emerald-500'],
                ['Aria K.',  'Head of AI',                'from-amber-500 to-rose-500'],
                ['Devon P.', 'Growth · ex-agency',        'from-indigo-500 to-violet-500'],
            ] as $m)
                <div class="reveal text-center">
                    <div class="mx-auto grid h-24 w-24 place-items-center rounded-3xl bg-gradient-to-br {{ $m[2] }} text-3xl font-black text-white shadow-lg">
                        {{ substr($m[0],0,1) }}
                    </div>
                    <div class="mt-4 font-bold text-slate-900">{{ $m[0] }}</div>
                    <div class="text-xs uppercase tracking-widest text-slate-500">{{ $m[1] }}</div>
                </div>
            @endforeach
        </div>
    </section>

    @include('marketing._cta', ['title' => 'Come build the creator commerce future with us.', 'sub' => 'We\'re hiring across engineering, design, and growth. Or just start using the product for free.'])
</x-layouts.app>
