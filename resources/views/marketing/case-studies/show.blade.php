@php
    $md = fn ($t) => \App\Support\BriefMarkdown::render((string) $t);
    $tone = fn ($t) => [
        'violet' => 'from-violet-500 to-pink-500',
        'emerald'=> 'from-emerald-500 to-teal-500',
        'amber'  => 'from-amber-500 to-orange-500',
        'rose'   => 'from-rose-500 to-pink-500',
        'sky'    => 'from-sky-500 to-blue-500',
        'slate'  => 'from-slate-700 to-slate-900',
    ][$t] ?? 'from-violet-500 to-pink-500';
@endphp
<x-layouts.app panel="guest"
    :title="$study->metaTitle()"
    :metaDescription="$study->metaDescription()"
    :ogImage="$study->ogImage()">

    @php
        $breadcrumbLd = [
            '@context' => 'https://schema.org', '@type' => 'BreadcrumbList',
            'itemListElement' => [
                ['@type' => 'ListItem', 'position' => 1, 'name' => 'CreatorPlex',  'item' => url('/')],
                ['@type' => 'ListItem', 'position' => 2, 'name' => 'Case studies', 'item' => route('case-studies.index')],
                ['@type' => 'ListItem', 'position' => 3, 'name' => $study->brand_name],
            ],
        ];
        $articleLd = [
            '@context'      => 'https://schema.org',
            '@type'         => 'Article',
            'headline'      => $study->headline,
            'datePublished' => $study->published_at?->toIso8601String(),
            'author'        => ['@type' => 'Organization', 'name' => 'CreatorPlex'],
            'image'         => $study->cover_image_path,
        ];
    @endphp
    <x-marketing.json-ld :blocks="[$breadcrumbLd, $articleLd]" />

    {{-- HERO --}}
    <section class="relative overflow-hidden bg-gradient-to-br from-violet-600 via-pink-500 to-amber-500 text-white">
        @if($study->cover_image_path)
            <img src="{{ $study->cover_image_path }}" alt="{{ $study->brand_name }}" class="absolute inset-0 h-full w-full object-cover opacity-40">
            <div class="absolute inset-0 bg-gradient-to-t from-black/70 to-transparent"></div>
        @endif
        <div class="relative mx-auto max-w-4xl px-4 pt-16 pb-14 md:pt-24 md:pb-20">
            <nav class="text-xs text-white/80"><a href="{{ route('case-studies.index') }}" class="hover:text-white">Case studies</a> → <span>{{ $study->brand_name }}</span></nav>
            <div class="mt-4 flex flex-wrap items-center gap-3 text-xs">
                <span class="rounded-full bg-white/20 px-3 py-1 font-bold backdrop-blur">{{ $study->brand_name }}</span>
                @if($study->industry)<span class="rounded-full bg-white/15 px-3 py-1 backdrop-blur">{{ $study->industry }}</span>@endif
                @if($study->city)<span class="rounded-full bg-white/15 px-3 py-1 backdrop-blur">📍 {{ $study->city }}</span>@endif
                @if($study->campaign_type)<span class="rounded-full bg-white/15 px-3 py-1 backdrop-blur capitalize">{{ $study->campaign_type }}</span>@endif
            </div>
            <h1 class="mt-4 text-4xl font-black tracking-tight sm:text-5xl md:text-6xl">{{ $study->headline }}</h1>
            @if($study->subheadline)
                <p class="mt-4 max-w-2xl text-lg text-white/90">{{ $study->subheadline }}</p>
            @endif
        </div>
    </section>

    {{-- METRICS --}}
    @if(! empty($study->metrics))
        <section class="-mt-8 mx-auto max-w-5xl px-4 relative z-10">
            <div class="grid gap-3 rounded-3xl border border-slate-200 bg-white p-4 shadow-lg sm:grid-cols-2 lg:grid-cols-{{ min(4, count($study->metrics)) }}">
                @foreach($study->metrics as $m)
                    <div class="rounded-2xl bg-gradient-to-br {{ $tone($m['tone'] ?? 'violet') }} p-4 text-white shadow-sm">
                        <p class="text-[10px] font-bold uppercase tracking-widest opacity-90">{{ $m['label'] }}</p>
                        <p class="mt-1 text-2xl font-black">{{ $m['value'] }}</p>
                    </div>
                @endforeach
            </div>
        </section>
    @endif

    {{-- BODY --}}
    <section class="mx-auto max-w-4xl px-4 py-14">
        @if($study->summary)
            <article class="brief-body prose prose-slate max-w-none">{!! $md($study->summary) !!}</article>
        @endif

        @if($study->challenge || $study->solution || $study->results)
            <div class="mt-10 grid gap-6 md:grid-cols-3">
                @foreach([['🎯 Challenge', $study->challenge, 'from-rose-500 to-pink-500'],['🛠 Solution', $study->solution, 'from-violet-500 to-indigo-500'],['📈 Results', $study->results, 'from-emerald-500 to-teal-500']] as [$title, $content, $g])
                    @if($content)
                        <div class="rounded-2xl border border-slate-200 bg-white p-5">
                            <div class="grid h-9 w-9 place-items-center rounded-xl bg-gradient-to-br {{ $g }} text-white shadow-sm">{{ substr($title, 0, strpos($title,' ')) }}</div>
                            <h2 class="mt-3 text-lg font-black text-slate-900">{{ trim(substr($title, strpos($title,' '))) }}</h2>
                            <div class="brief-body prose prose-slate prose-sm mt-3 max-w-none">{!! $md($content) !!}</div>
                        </div>
                    @endif
                @endforeach
            </div>
        @endif

        @if($study->quote)
            <blockquote class="mt-14 rounded-3xl bg-gradient-to-br from-slate-900 to-slate-950 p-8 text-white md:p-12">
                <p class="text-2xl font-black leading-snug md:text-3xl">"{{ $study->quote }}"</p>
                @if($study->quote_author)
                    <footer class="mt-4 text-sm text-white/70">— {{ $study->quote_author }}{{ $study->quote_role ? ', '.$study->quote_role : '' }}</footer>
                @endif
            </blockquote>
        @endif
    </section>

    @if($related->isNotEmpty())
        <section class="mx-auto max-w-6xl px-4 pb-16">
            <h2 class="text-2xl font-black text-slate-900">More case studies</h2>
            <div class="mt-6 grid gap-6 md:grid-cols-3">
                @foreach($related as $r)
                    <a href="{{ route('case-studies.show', $r->slug) }}" class="card card-hover group flex flex-col overflow-hidden">
                        <div class="h-32 bg-gradient-to-br from-violet-500 via-pink-500 to-amber-500">
                            @if($r->cover_image_path)<img src="{{ $r->cover_image_path }}" class="h-full w-full object-cover">@endif
                        </div>
                        <div class="flex-1 p-5">
                            <p class="text-xs font-bold uppercase tracking-widest text-violet-600">{{ $r->brand_name }}</p>
                            <h3 class="mt-1 font-black text-slate-900">{{ $r->headline }}</h3>
                        </div>
                    </a>
                @endforeach
            </div>
        </section>
    @endif

    @include('marketing._cta')
</x-layouts.app>
