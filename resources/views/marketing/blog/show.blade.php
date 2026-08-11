<x-layouts.app panel="guest" :title="$post['title']">
    <x-slot name="metaDescription">{{ $post['meta_description'] ?? ($post['excerpt'] ?? '') }}</x-slot>
    <x-slot name="canonical">{{ $post['canonical_url'] ?? route('blog.show', $post['slug']) }}</x-slot>

    {{-- JSON-LD: Article --}}
    <script type="application/ld+json">
    {!! json_encode([
        '@context' => 'https://schema.org',
        '@type'    => 'BlogPosting',
        'headline' => $post['title'],
        'datePublished' => $post['date'] ?? null,
        'author'   => ['@type' => 'Person', 'name' => $post['author'] ?? 'CreatorFlow'],
        'publisher'=> ['@type' => 'Organization', 'name' => 'CreatorFlow', 'url' => url('/')],
        'mainEntityOfPage' => route('blog.show', $post['slug']),
        'description' => $post['meta_description'] ?? ($post['excerpt'] ?? ''),
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
    </script>

    @if(! empty($faqJson) && is_array($faqJson))
    <script type="application/ld+json">
    {!! json_encode([
        '@context' => 'https://schema.org',
        '@type'    => 'FAQPage',
        'mainEntity' => collect($faqJson)->map(fn ($qa) => [
            '@type' => 'Question',
            'name'  => $qa['q'] ?? '',
            'acceptedAnswer' => ['@type' => 'Answer', 'text' => $qa['a'] ?? ''],
        ])->all(),
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
    </script>
    @endif

    <article>
        {{-- Header --}}
        <div class="relative overflow-hidden">
            <div class="aurora"></div>
            <div class="mx-auto max-w-3xl px-4 pb-8 pt-14 md:pt-20">
                <nav class="text-xs text-slate-500">
                    <a href="{{ route('blog.index') }}" class="hover:text-slate-900">Blog</a> → <span>{{ $post['category'] }}</span>
                </nav>
                <span class="chip mt-4"><span class="chip-dot"></span> {{ $post['category'] }} · {{ $post['read'] }} read</span>
                <h1 class="mt-4 text-4xl font-black leading-[1.1] tracking-tight text-slate-900 sm:text-5xl">{{ $post['title'] }}</h1>
                <div class="mt-6 flex items-center gap-3">
                    <span class="grid h-11 w-11 place-items-center rounded-full bg-gradient-to-br {{ $post['grad'] }} text-sm font-bold text-white">{{ substr($post['author'],0,1) }}</span>
                    <div class="text-sm">
                        <div class="font-bold text-slate-900">{{ $post['author'] }}</div>
                        <div class="text-slate-500">{{ \Illuminate\Support\Carbon::parse($post['date'])->format('F j, Y') }}</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Hero banner --}}
        <div class="mx-auto max-w-5xl px-4">
            <div class="relative h-56 overflow-hidden rounded-3xl bg-gradient-to-br {{ $post['grad'] }} shadow-lg md:h-80">
                @if(! empty($post['cover_image_path']))
                    <img src="{{ $post['cover_image_path'] }}" alt="{{ $post['title'] }}" class="h-full w-full object-cover">
                @else
                    <div class="absolute inset-0 opacity-30" style="background: radial-gradient(400px 400px at 30% 30%, rgba(255,255,255,.55), transparent 60%);"></div>
                @endif
            </div>
        </div>

        {{-- Body --}}
        <div class="mx-auto max-w-3xl px-4 py-14">
            @if(! empty($post['body']))
                <x-brief :markdown="$post['body']" />
            @else
                <div class="prose prose-slate max-w-none prose-headings:font-black prose-headings:tracking-tight prose-a:text-violet-700 prose-a:no-underline hover:prose-a:text-violet-900">
                    <p class="text-lg text-slate-600">{!! $post['excerpt'] !!}</p>
                    <h2>The short version</h2>
                    <p>
                        Every week we ship this product to more brands, and every week we learn something new about what
                        actually works in creator marketing in 2025. This post distils the last month's learnings into one
                        read you can share with your team.
                    </p>
                    <h2>1. Start with the hero, not the campaign</h2>
                    <p>The biggest mistake we see: teams pick a campaign type first (barter! UGC! reviews!) and then hunt for creators to fill it. Reverse the flow.</p>
                    <blockquote>"We stopped booking creators by follower count and started booking by audience overlap. Our CPA dropped 42% in six weeks." — <em>Growth lead, DTC beauty brand</em></blockquote>
                    <h2>2. Seeding is bottom-of-funnel, not brand awareness</h2>
                    <p>Contrary to how most tools frame it, seeding at scale is the fastest way to generate content for paid social.</p>
                    <h2>The takeaway</h2>
                    <p>Ship one hero, seed at 3× your target, measure what actually paid for itself, then double down.</p>
                </div>
            @endif

            {{-- FAQ section (renders human-readable version alongside JSON-LD above) --}}
            @if(! empty($faqJson) && is_array($faqJson))
                <section class="mt-14">
                    <h2 class="text-2xl font-black tracking-tight text-slate-900">Frequently asked questions</h2>
                    <div class="mt-6 divide-y divide-slate-200 rounded-2xl border border-slate-200 bg-white">
                        @foreach($faqJson as $qa)
                            <details class="group p-5">
                                <summary class="flex cursor-pointer items-center justify-between font-semibold text-slate-900">
                                    {{ $qa['q'] ?? '' }}
                                    <svg class="h-5 w-5 text-slate-400 transition group-open:rotate-180" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" d="M6 9l6 6 6-6"/></svg>
                                </summary>
                                <p class="mt-3 text-sm text-slate-600">{{ $qa['a'] ?? '' }}</p>
                            </details>
                        @endforeach
                    </div>
                </section>
            @endif

            {{-- Author card --}}
            <div class="mt-10 flex items-center gap-4 rounded-2xl border border-slate-200 bg-white p-5">
                <span class="grid h-14 w-14 place-items-center rounded-2xl bg-gradient-to-br {{ $post['grad'] }} text-xl font-black text-white">{{ substr($post['author'],0,1) }}</span>
                <div>
                    <div class="text-sm font-bold text-slate-900">{{ $post['author'] }}</div>
                    <div class="text-xs text-slate-500">Writes about creator marketing, DTC ops, and the ugly parts of scale.</div>
                </div>
            </div>
        </div>
    </article>

    {{-- Related --}}
    <section class="mx-auto max-w-6xl px-4 pb-16">
        <h2 class="section-title">Keep reading</h2>
        <div class="mt-6 grid gap-6 md:grid-cols-3">
            @foreach($related as $r)
                <a href="{{ route('blog.show', $r['slug']) }}" class="card card-hover flex flex-col overflow-hidden">
                    <div class="h-32 bg-gradient-to-br {{ $r['grad'] }}"></div>
                    <div class="flex-1 p-5">
                        <div class="text-xs font-semibold uppercase tracking-widest text-violet-700">{{ $r['category'] }}</div>
                        <h3 class="mt-2 font-bold text-slate-900">{{ $r['title'] }}</h3>
                    </div>
                </a>
            @endforeach
        </div>
    </section>

    @include('marketing._cta')
</x-layouts.app>
