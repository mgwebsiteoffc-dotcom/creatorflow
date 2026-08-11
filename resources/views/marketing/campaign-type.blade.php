<x-layouts.app panel="guest"
    :title="strip_tags($item['title']).' campaigns'"
    :metaDescription="$item['meta'] ?? null"
    :canonical="route('campaign-type.show', $slug)">

    <script type="application/ld+json">
    {!! json_encode([
        '@context' => 'https://schema.org', '@type' => 'Service',
        'name'     => strip_tags($item['title']).' campaigns',
        'provider' => ['@type' => 'Organization', 'name' => 'CreatorPlex', 'url' => url('/')],
        'url'      => route('campaign-type.show', $slug),
        'description' => $item['meta'] ?? $item['tagline'],
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

    {{-- HERO --}}
    <section class="relative overflow-hidden">
        <div class="aurora"></div>
        <div class="dotted absolute inset-0 -z-10"></div>

        <div class="mx-auto grid max-w-6xl gap-10 px-4 pb-16 pt-16 md:grid-cols-2 md:items-center md:gap-14 md:pt-24">
            <div>
                <span class="reveal chip"><span class="chip-dot"></span> Campaign type · {{ $item['title'] }}</span>
                <h1 class="reveal mt-5 text-4xl font-black leading-[1.05] tracking-tight text-slate-900 sm:text-5xl md:text-6xl">
                    {{ $item['title'] }} <span class="text-gradient">on autopilot</span>
                </h1>
                <p class="reveal mt-5 max-w-lg text-lg text-slate-600">{{ $item['tagline'] }}</p>

                @if(! empty($item['hero_kpis']))
                    <div class="reveal mt-8 grid max-w-md grid-cols-3 gap-3">
                        @foreach($item['hero_kpis'] as $k)
                            <div class="rounded-2xl border border-slate-200 bg-white p-3 text-center shadow-sm">
                                <div class="text-lg font-black text-slate-900">{{ $k[0] }}</div>
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

            <div class="reveal">
                <div class="relative">
                    <div class="absolute -inset-6 -z-10 rounded-[2rem] blur-2xl"
                         style="background-image: linear-gradient(120deg, {{ $item['accent'] ?? '#7c3aed' }}55, #ec489933);"></div>
                    <div class="g-border p-1 shadow-[0_30px_80px_-30px_rgba(15,23,42,.25)]">
                        <div class="rounded-[calc(1.25rem-1px)] bg-white p-6">
                            <div class="grid h-20 w-20 place-items-center rounded-2xl bg-gradient-to-br {{ $item['grad'] }} text-4xl text-white shadow-md">{{ $item['emoji'] }}</div>
                            <div class="mt-4 text-xs font-semibold uppercase tracking-widest text-slate-500">Campaign type</div>
                            <div class="mt-1 text-xl font-black text-slate-900">{{ $item['title'] }}</div>
                            <p class="mt-2 text-sm text-slate-600">{{ $item['tagline'] }}</p>

                            @if(! empty($item['features']))
                                <div class="mt-5 space-y-2">
                                    @foreach($item['features'] as $f)
                                        <div class="flex items-start gap-3 rounded-xl bg-slate-50 p-3">
                                            <span class="text-lg">{{ $f['icon'] }}</span>
                                            <div>
                                                <div class="text-sm font-bold text-slate-900">{{ $f['title'] }}</div>
                                                <div class="text-xs text-slate-500">{{ $f['body'] }}</div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- WHY --}}
    <section class="mx-auto max-w-6xl px-4 py-16">
        <div class="mx-auto max-w-2xl text-center">
            <p class="section-eyebrow reveal">Why this format</p>
            <h2 class="section-title reveal mt-3">{{ $item['when_headline'] ?? 'When to run this' }}</h2>
            @if(! empty($item['when_body']))
                <p class="section-sub reveal mt-3">{{ $item['when_body'] }}</p>
            @endif
        </div>
    </section>

    {{-- PROCESS ============= --}}
    @if(! empty($item['process']))
        <section class="relative overflow-hidden py-16">
            <div class="absolute inset-0 -z-10 bg-gradient-to-b from-slate-50 to-white"></div>
            <div class="mx-auto max-w-6xl px-4">
                <div class="mx-auto max-w-2xl text-center">
                    <p class="section-eyebrow reveal">How it works</p>
                    <h2 class="section-title reveal mt-3">From launch to attribution</h2>
                </div>

                <div class="mt-12 grid gap-6 md:grid-cols-4">
                    @foreach($item['process'] as $p)
                        <div class="reveal relative card card-hover p-5">
                            <div class="grid h-10 w-10 place-items-center rounded-xl bg-gradient-to-br {{ $item['grad'] }} text-sm font-black text-white shadow-sm">{{ $p['step'] }}</div>
                            <div class="mt-4 font-bold text-slate-900">{{ $p['title'] }}</div>
                            <div class="mt-1 text-sm text-slate-600">{{ $p['body'] }}</div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- PRICING TABLE (only if 'pricing' key present) --}}
    @if(! empty($item['pricing']))
        <section class="mx-auto max-w-6xl px-4 py-16">
            <div class="mx-auto max-w-2xl text-center">
                <p class="section-eyebrow reveal">Rate card</p>
                <h2 class="section-title reveal mt-3">Fair pricing by creator tier</h2>
                <p class="section-sub reveal mt-3">Benchmarks from 12,000+ CreatorPlex deals in India.</p>
            </div>
            <div class="reveal mt-10 grid gap-4 md:grid-cols-4">
                @foreach($item['pricing'] as $p)
                    <div class="card p-6">
                        <div class="text-xs font-semibold uppercase tracking-widest text-violet-700">{{ $p['tier'] }}</div>
                        <div class="mt-1 text-xs text-slate-500">{{ $p['range'] }} followers</div>
                        <div class="mt-4 text-xl font-black text-slate-900">{{ $p['price'] }}</div>
                    </div>
                @endforeach
            </div>
        </section>
    @endif

    {{-- SCREENSHOT SPOTLIGHT --}}
    <section class="mx-auto max-w-6xl px-4 py-16">
        <div class="grid gap-10 md:grid-cols-2 md:items-center">
            <div class="reveal">
                <p class="section-eyebrow">Inside the platform</p>
                <h2 class="section-title mt-3 text-left">See {{ strtolower(strip_tags($item['title'])) }} in action</h2>
                <p class="mt-4 text-slate-600">One dashboard for briefs, applications, content approval, and attribution. Real screenshots, no marketing collateral.</p>
                <a href="{{ route('features') }}" class="btn-gradient mt-6">Explore all features →</a>
            </div>
            <div class="reveal">
                @include('marketing._app-screenshot', ['variant' => 2])
            </div>
        </div>
    </section>

    {{-- FAQ --}}
    @if(! empty($item['faqs']))
        <section class="mx-auto max-w-3xl px-4 py-16">
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

    @include('marketing._lead-form', [
        'source' => 'campaign-type:'.$slug,
        'title'  => 'Ready to launch a '.strip_tags($item['title']).' campaign?',
        'sub'    => 'Talk to our team — we\'ll scope your first campaign in a 15-minute call.',
    ])

    {{-- OTHER FORMATS --}}
    <section class="mx-auto max-w-6xl px-4 pb-20">
        <div class="mx-auto max-w-2xl text-center">
            <p class="section-eyebrow reveal">Other formats</p>
            <h2 class="section-title reveal mt-3">Pick the right playbook</h2>
        </div>
        <div class="mt-10 grid gap-5 md:grid-cols-3">
            @foreach($all as $s => $it)
                @if($s !== $slug)
                    <a href="{{ route('campaign-type.show', $s) }}" class="reveal card card-hover flex items-start gap-3 p-5">
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
