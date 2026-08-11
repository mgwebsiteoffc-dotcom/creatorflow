<x-layouts.app panel="guest" :title="strip_tags($item['title']).' campaigns'" :metaDescription="$item['meta'] ?? null" :canonical="route('campaign-type.show', $slug)">

    <script type="application/ld+json">
    {!! json_encode([
        '@context' => 'https://schema.org',
        '@type'    => 'Service',
        'name'     => strip_tags($item['title']).' campaigns',
        'provider' => ['@type' => 'Organization', 'name' => 'CreatorFlow', 'url' => url('/')],
        'url'      => route('campaign-type.show', $slug),
        'description' => $item['meta'] ?? $item['tagline'],
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

    <section class="relative overflow-hidden">
        <div class="aurora"></div>
        <div class="dotted absolute inset-0 -z-10"></div>
        <div class="mx-auto max-w-5xl px-4 pb-14 pt-16 text-center md:pt-24">
            <span class="chip reveal mx-auto"><span class="chip-dot"></span> Campaign type · {!! $item['title'] !!}</span>
            <h1 class="reveal mt-5 text-4xl font-black leading-[1.05] tracking-tight text-slate-900 sm:text-5xl md:text-6xl">
                {!! $item['title'] !!} campaigns <span class="text-gradient">on autopilot</span>
            </h1>
            <p class="reveal mx-auto mt-5 max-w-2xl text-lg text-slate-600">{{ $item['tagline'] }}</p>
            <div class="reveal mt-8 flex flex-wrap justify-center gap-3">
                <a href="{{ route('register') }}" class="btn-gradient">Start free</a>
                <a href="#lead-form" class="btn-glass">Talk to sales</a>
            </div>
        </div>
    </section>

    <section class="mx-auto max-w-6xl px-4">
        <div class="reveal overflow-hidden rounded-3xl bg-gradient-to-br {{ $item['grad'] }} p-8 text-white shadow-lg md:p-12">
            <div class="text-5xl">{{ $item['emoji'] }}</div>
            <p class="mt-4 max-w-2xl text-white/90">{{ $item['why'] }}</p>
        </div>

        <div class="reveal mt-8 grid gap-6 md:grid-cols-3">
            @foreach($item['bullets'] as $b)
                <div class="card p-6">
                    <div class="grid h-10 w-10 place-items-center rounded-xl bg-gradient-to-br {{ $item['grad'] }} text-white">✓</div>
                    <p class="mt-3 font-semibold text-slate-900">{{ $b }}</p>
                </div>
            @endforeach
        </div>
    </section>

    @if(! empty($item['body']))
        <section class="mx-auto max-w-3xl px-4 py-16">
            <div class="reveal">
                <x-brief :markdown="$item['body']" />
            </div>
        </section>
    @endif

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

    @include('marketing._lead-form', ['source' => 'campaign-type:'.$slug, 'title' => 'Ready to launch a '.strip_tags($item['title']).' campaign?', 'sub' => 'Talk to our team — we\'ll help you scope your first campaign in a 15-minute call.'])

    <section class="mx-auto max-w-6xl px-4 pb-20">
        <div class="mx-auto max-w-2xl text-center">
            <p class="section-eyebrow reveal">Explore other formats</p>
            <h2 class="section-title reveal mt-3">Pick the right playbook</h2>
        </div>
        <div class="mt-10 grid gap-5 md:grid-cols-3">
            @foreach($all as $s => $it)
                @if($s !== $slug)
                    <a href="{{ route('campaign-type.show', $s) }}" class="reveal card card-hover flex items-start gap-3 p-5">
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
