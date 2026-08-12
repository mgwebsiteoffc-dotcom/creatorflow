<x-layouts.app panel="guest"
    :title="strip_tags($item['title']).' influencer marketing'"
    :metaDescription="$item['meta'] ?? null"
    :canonical="route('industry.show', $slug)">

    {{-- JSON-LD --}}
    <script type="application/ld+json">
    {!! json_encode([
        '@context' => 'https://schema.org', '@type' => 'WebPage',
        'name' => strip_tags($item['title']).' influencer marketing',
        'url'  => route('industry.show', $slug),
        'description' => $item['meta'] ?? $item['tagline'],
        'about' => strip_tags($item['title']),
    ], JSON_UNESCAPED_SLASHES) !!}
    </script>
    @if(! empty($item['faqs']))
        <script type="application/ld+json">
        {!! json_encode([
            '@context' => 'https://schema.org', '@type' => 'FAQPage',
            'mainEntity' => collect($item['faqs'])->map(fn ($qa) => [
                '@type' => 'Question', 'name' => $qa['q'],
                'acceptedAnswer' => ['@type' => 'Answer', 'text' => $qa['a']],
            ])->all(),
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
        </script>
    @endif

    {{-- ============ HERO ============ --}}
    <section class="relative overflow-hidden">
        <div class="aurora"></div>
        <div class="dotted absolute inset-0 -z-10"></div>

        <div class="mx-auto grid max-w-6xl gap-10 px-4 pb-16 pt-16 md:grid-cols-2 md:items-center md:gap-14 md:pt-24">
            <div>
                <span class="reveal chip"><span class="chip-dot"></span> Influencer marketing · {{ $item['title'] }}</span>
                <h1 class="reveal mt-5 text-4xl font-black leading-[1.05] tracking-tight text-slate-900 sm:text-5xl md:text-6xl">
                    {{ $item['title'] }} campaigns that <span class="text-gradient">convert</span>
                </h1>
                <p class="reveal mt-5 max-w-lg text-lg text-slate-600">{{ $item['tagline'] }}</p>

                @if(! empty($item['hero_kpis']))
                    <div class="reveal mt-8 grid max-w-md grid-cols-3 gap-3">
                        @foreach($item['hero_kpis'] as $k)
                            <div class="rounded-2xl border border-slate-200 bg-white p-3 text-center shadow-sm">
                                <div class="text-2xl font-black text-slate-900">{{ $k[0] }}</div>
                                <div class="mt-0.5 text-[10px] font-semibold uppercase tracking-widest text-slate-500">{{ $k[1] }}</div>
                            </div>
                        @endforeach
                    </div>
                @endif

                <div class="reveal mt-8 flex flex-wrap gap-3">
                    <a href="{{ route('register') }}" class="btn-gradient">Start free</a>
                    <a href="#lead-form" class="btn-glass">Talk to sales</a>
                </div>
            </div>

            {{-- Hero visual: campaign card mockup themed to industry --}}
            <div class="reveal">
                <div class="relative">
                    <div class="absolute -inset-6 -z-10 rounded-[2rem] blur-2xl"
                         style="background-image: linear-gradient(120deg, {{ $item['accent'] ?? '#7c3aed' }}55, #7c3aed33);"></div>
                    <div class="g-border p-1 shadow-[0_30px_80px_-30px_rgba(15,23,42,.25)]">
                        <div class="rounded-[calc(1.25rem-1px)] bg-white p-5">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                                                                                                                            </div>
                                <span class="rounded-full bg-emerald-100 px-2 py-0.5 text-[10px] font-bold text-emerald-700">● LIVE</span>
                            </div>
                            <div class="mt-4 rounded-2xl p-5 text-white shadow-inner"
                                 style="background-image: linear-gradient(135deg, var(--tw-gradient-stops));"
                                 class="bg-gradient-to-br {{ $item['grad'] }}">
                                <div class="text-4xl">{{ $item['emoji'] }}</div>
                                <div class="mt-3 text-xs font-semibold uppercase tracking-widest opacity-90">Campaign</div>
                                <div class="text-lg font-black leading-tight">{{ $item['title'] }} launch</div>
                                <div class="mt-4 grid grid-cols-3 gap-2 text-center">
                                    @foreach($item['hero_kpis'] ?? [] as $k)
                                        <div class="rounded-lg bg-white/15 p-2 backdrop-blur">
                                            <div class="text-[9px] uppercase tracking-widest opacity-80">{{ $k[1] }}</div>
                                            <div class="text-sm font-black">{{ $k[0] }}</div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <div class="mt-4 rounded-xl bg-slate-50 p-3">
                                <div class="text-[11px] font-semibold text-slate-500">Latest applicants</div>
                                <div class="mt-2 space-y-1.5">
                                    @foreach(['#f472b6' => ['A','Aria K.','98%'], '#a78bfa' => ['T','Theo V.','92%'], '#22d3ee' => ['N','Nova E.','88%']] as $c => $a)
                                        <div class="flex items-center gap-2 rounded-md bg-white px-2 py-1.5 text-[11px] shadow-sm">
                                            <span class="grid h-6 w-6 place-items-center rounded-full text-[10px] font-bold text-white" style="background:{{ $c }}">{{ $a[0] }}</span>
                                            <div class="flex-1 truncate font-semibold text-slate-800">{{ $a[1] }}</div>
                                            <span class="font-bold text-emerald-600">{{ $a[2] }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="absolute -left-3 -bottom-3 hidden rounded-2xl bg-white p-3 shadow-lg ring-1 ring-slate-100 md:block">
                        <div class="flex items-center gap-2 text-xs">
                            <span class="grid h-6 w-6 place-items-center rounded-lg bg-emerald-100 text-emerald-700">$</span>
                            <span class="font-semibold text-slate-800">₹4.2L attributed</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ============ TRUSTED-BY ============ --}}
    @if(! empty($item['brands']))
        <section class="border-y border-slate-200 bg-white/60 py-8">
            <div class="mx-auto max-w-6xl px-4">
                <p class="text-center text-[11px] font-bold uppercase tracking-widest text-slate-500">
                    {{ ucwords(strtolower(strip_tags($item['title']))) }} brands running on CreatorPlex
                </p>
                <div class="marquee mt-4">
                    <div class="marquee-track">
                        @foreach(array_merge($item['brands'], $item['brands']) as $b)
                            <div class="flex shrink-0 items-center gap-2 text-xl font-black tracking-tight text-slate-400">
                                                                {{ $b }}
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>
    @endif

    {{-- ============ WHY (icon cards) ============ --}}
    @if(! empty($item['why_points']))
        <section class="mx-auto max-w-6xl px-4 py-20">
            <div class="mx-auto max-w-2xl text-center">
                <p class="section-eyebrow reveal">Why it works</p>
                <h2 class="section-title reveal mt-3">{{ $item['why_headline'] ?? 'Why creators win here' }}</h2>
                @if(! empty($item['why_body']))
                    <p class="section-sub reveal mt-3">{{ $item['why_body'] }}</p>
                @endif
            </div>
            <div class="mt-12 grid gap-5 md:grid-cols-3">
                @foreach($item['why_points'] as $p)
                    <div class="reveal card card-hover p-6">
                        <div class="grid h-11 w-11 place-items-center rounded-xl bg-gradient-to-br {{ $item['grad'] }} text-lg text-white shadow-sm">{{ $p['icon'] }}</div>
                        <h3 class="mt-4 font-bold text-slate-900">{{ $p['title'] }}</h3>
                        <p class="mt-1 text-sm text-slate-600">{{ $p['body'] }}</p>
                    </div>
                @endforeach
            </div>
        </section>
    @endif

    {{-- ============ PLAYBOOK · sticky screenshot ============ --}}
    @if(! empty($item['playbook']))
        <section class="relative overflow-hidden py-20">
            <div class="absolute inset-0 -z-10 bg-gradient-to-b from-slate-50 to-white"></div>
            <div class="mx-auto max-w-6xl px-4">
                <div class="mx-auto max-w-2xl text-center">
                    <p class="section-eyebrow reveal">Playbook</p>
                    <h2 class="section-title reveal mt-3">The 4-step {{ strtolower(strip_tags($item['title'])) }} playbook</h2>
                </div>

                <div class="mt-12 grid gap-10 md:grid-cols-2 md:items-start">
                    <ol class="space-y-4">
                        @foreach($item['playbook'] as $step)
                            <li class="reveal flex gap-4 rounded-2xl border border-slate-200 bg-white p-5">
                                <div class="grid h-10 w-10 shrink-0 place-items-center rounded-xl bg-gradient-to-br {{ $item['grad'] }} text-sm font-black text-white shadow-sm">{{ $step['step'] }}</div>
                                <div>
                                    <div class="font-bold text-slate-900">{{ $step['title'] }}</div>
                                    <div class="mt-1 text-sm text-slate-600">{{ $step['body'] }}</div>
                                </div>
                            </li>
                        @endforeach
                    </ol>
                    <div class="reveal md:sticky md:top-24">
                        @include('marketing._app-screenshot', ['variant' => 0])
                    </div>
                </div>
            </div>
        </section>
    @endif

    {{-- ============ CASE STUDIES ============ --}}
    @if(! empty($item['examples']))
        <section class="mx-auto max-w-6xl px-4 py-20">
            <div class="mx-auto max-w-2xl text-center">
                <p class="section-eyebrow reveal">Case studies</p>
                <h2 class="section-title reveal mt-3">Brands winning with us</h2>
            </div>
            <div class="mt-10 grid gap-6 md:grid-cols-2">
                @foreach($item['examples'] as $ex)
                    <div class="reveal relative overflow-hidden rounded-3xl bg-gradient-to-br {{ $ex['grad'] }} p-7 text-white shadow-lg">
                        <div class="pointer-events-none absolute -right-6 -top-6 h-32 w-32 rounded-full bg-white/15 blur-2xl"></div>
                        <div class="relative">
                            <div class="text-xs font-bold uppercase tracking-widest opacity-80">Case study</div>
                            <div class="mt-1 text-2xl font-black">{{ $ex['name'] }}</div>
                            <p class="mt-4 text-white/90">{{ $ex['result'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>
    @endif

    {{-- ============ TESTIMONIAL ============ --}}
    @if(! empty($item['testimonial']))
        <section class="mx-auto max-w-4xl px-4 pb-20">
            <figure class="reveal card p-8 md:p-10">
                <div class="flex text-2xl text-amber-500">★★★★★</div>
                <blockquote class="mt-4 text-xl font-medium leading-relaxed text-slate-800 md:text-2xl">"{{ $item['testimonial']['q'] }}"</blockquote>
                <figcaption class="mt-6 flex items-center gap-3">
                    <span class="grid h-11 w-11 place-items-center rounded-full bg-gradient-to-br {{ $item['grad'] }} text-lg font-black text-white">{{ substr($item['testimonial']['name'], 0, 1) }}</span>
                    <div>
                        <div class="text-sm font-bold text-slate-900">{{ $item['testimonial']['name'] }}</div>
                        <div class="text-xs text-slate-500">{{ $item['testimonial']['company'] }}</div>
                    </div>
                </figcaption>
            </figure>
        </section>
    @endif

    {{-- ============ FAQ ============ --}}
    @if(! empty($item['faqs']))
        <section class="mx-auto max-w-3xl px-4 pb-20">
            <div class="text-center">
                <p class="section-eyebrow reveal">FAQ</p>
                <h2 class="section-title reveal mt-3">Common questions</h2>
            </div>
            <div class="reveal mt-8 divide-y divide-slate-200 rounded-2xl border border-slate-200 bg-white">
                @foreach($item['faqs'] as $qa)
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
    @endif

    {{-- ============ LEAD FORM ============ --}}
    @include('marketing._lead-form', [
        'source' => 'industry:'.$slug,
        'title'  => 'Ready to launch a '.strip_tags($item['title']).' campaign?',
        'sub'    => 'Talk to us — one of our specialists will map out your first campaign for free.',
    ])

    {{-- ============ OTHER INDUSTRIES ============ --}}
    <section class="mx-auto max-w-6xl px-4 pb-20">
        <div class="mx-auto max-w-2xl text-center">
            <p class="section-eyebrow reveal">Other industries</p>
            <h2 class="section-title reveal mt-3">Explore more</h2>
        </div>
        <div class="mt-10 grid gap-5 md:grid-cols-3">
            @foreach($all as $s => $it)
                @if($s !== $slug)
                    <a href="{{ route('industry.show', $s) }}" class="reveal card card-hover flex items-start gap-3 p-5">
                        <div class="grid h-11 w-11 shrink-0 place-items-center rounded-xl bg-gradient-to-br {{ $it['grad'] }} text-lg text-white shadow-sm">{{ $it['emoji'] }}</div>
                        <div class="min-w-0">
                            <h3 class="truncate font-bold text-slate-900">{{ $it['title'] }}</h3>
                            <p class="mt-1 text-xs text-slate-500">{{ $it['tagline'] }}</p>
                        </div>
                    </a>
                @endif
            @endforeach
        </div>
    </section>

    @include('marketing._cta')
</x-layouts.app>
