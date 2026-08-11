<x-layouts.app panel="guest" :title="strip_tags($item['title']).' · influencer marketing'" :metaDescription="$item['meta'] ?? null" :canonical="route('industry.show', $slug)">

    {{-- JSON-LD: WebPage + FAQPage --}}
    <script type="application/ld+json">
    {!! json_encode([
        '@context' => 'https://schema.org',
        '@type'    => 'WebPage',
        'name'     => strip_tags($item['title']).' influencer marketing',
        'url'      => route('industry.show', $slug),
        'description' => $item['meta'] ?? $item['tagline'],
        'about'    => strip_tags($item['title']),
    ], JSON_UNESCAPED_SLASHES) !!}
    </script>
    @if(! empty($item['faqs']))
        <script type="application/ld+json">
        {!! json_encode([
            '@context' => 'https://schema.org',
            '@type'    => 'FAQPage',
            'mainEntity' => collect($item['faqs'])->map(fn ($qa) => [
                '@type' => 'Question',
                'name'  => $qa['q'],
                'acceptedAnswer' => ['@type' => 'Answer', 'text' => $qa['a']],
            ])->all(),
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
        </script>
    @endif

    {{-- HERO --}}
    <section class="relative overflow-hidden">
        <div class="aurora"></div>
        <div class="dotted absolute inset-0 -z-10"></div>
        <div class="mx-auto max-w-5xl px-4 pb-14 pt-16 text-center md:pt-24">
            <span class="chip reveal mx-auto"><span class="chip-dot"></span> Influencer marketing · {!! $item['title'] !!}</span>
            <h1 class="reveal mt-5 text-4xl font-black leading-[1.05] tracking-tight text-slate-900 sm:text-5xl md:text-6xl">
                {!! $item['title'] !!} campaigns that <span class="text-gradient">convert</span>
            </h1>
            <p class="reveal mx-auto mt-5 max-w-2xl text-lg text-slate-600">{{ $item['tagline'] }}</p>
            <div class="reveal mt-8 flex flex-wrap justify-center gap-3">
                <a href="{{ route('register') }}" class="btn-gradient">Start free</a>
                <a href="#lead-form" class="btn-glass">Talk to sales</a>
            </div>
        </div>
    </section>

    {{-- Banner + stats --}}
    <section class="mx-auto max-w-6xl px-4">
        <div class="reveal overflow-hidden rounded-3xl bg-gradient-to-br {{ $item['grad'] }} p-8 text-white shadow-lg md:p-12">
            <div class="text-5xl">{{ $item['emoji'] }}</div>
            <p class="mt-4 max-w-2xl text-white/90">{{ $item['why'] }}</p>
            <div class="mt-6 grid gap-3 sm:grid-cols-3 md:max-w-lg">
                @foreach($item['stats'] as $s)
                    <div class="rounded-2xl bg-white/15 p-4 backdrop-blur">
                        <div class="text-[10px] uppercase tracking-widest opacity-80">{{ $s[0] }}</div>
                        <div class="text-2xl font-black">{{ $s[1] }}</div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Long-form content --}}
    @if(! empty($item['body']))
        <section class="mx-auto max-w-3xl px-4 py-16">
            <div class="reveal">
                <x-brief :markdown="$item['body']" />
            </div>
        </section>
    @endif

    {{-- FAQ --}}
    @if(! empty($item['faqs']))
        <section class="mx-auto max-w-3xl px-4 pb-16">
            <div class="text-center">
                <p class="section-eyebrow reveal">FAQ</p>
                <h2 class="section-title reveal mt-3">Common questions</h2>
            </div>
            <div class="reveal mt-8 divide-y divide-slate-200 rounded-2xl border border-slate-200 bg-white">
                @foreach($item['faqs'] as $qa)
                    <details class="group p-5">
                        <summary class="flex cursor-pointer items-center justify-between font-semibold text-slate-900">
                            {{ $qa['q'] }}
                            <svg class="h-5 w-5 text-slate-400 transition group-open:rotate-180" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" d="M6 9l6 6 6-6"/></svg>
                        </summary>
                        <p class="mt-3 text-sm text-slate-600">{{ $qa['a'] }}</p>
                    </details>
                @endforeach
            </div>
        </section>
    @endif

    {{-- LEAD FORM --}}
    @include('marketing._lead-form', ['source' => 'industry:'.$slug, 'title' => 'Ready to launch a '.strip_tags($item['title']).' campaign?', 'sub' => 'Talk to us — one of our specialists will map out your first campaign for free.'])

    {{-- Related industries --}}
    <section class="mx-auto max-w-6xl px-4 pb-20">
        <div class="mx-auto max-w-2xl text-center">
            <p class="section-eyebrow reveal">Other industries</p>
            <h2 class="section-title reveal mt-3">Campaigns that work for every niche</h2>
        </div>
        <div class="mt-10 grid gap-5 md:grid-cols-3">
            @foreach($all as $s => $it)
                @if($s !== $slug)
                    <a href="{{ route('industry.show', $s) }}" class="reveal card card-hover flex items-start gap-3 p-5">
                        <div class="grid h-11 w-11 shrink-0 place-items-center rounded-xl bg-gradient-to-br {{ $it['grad'] }} text-lg text-white shadow-sm">{{ $it['emoji'] }}</div>
                        <div>
                            <h3 class="font-bold text-slate-900">{!! $it['title'] !!}</h3>
                            <p class="mt-1 text-xs text-slate-500">{{ $it['tagline'] }}</p>
                        </div>
                    </a>
                @endif
            @endforeach
        </div>
    </section>

    @include('marketing._cta')
</x-layouts.app>
