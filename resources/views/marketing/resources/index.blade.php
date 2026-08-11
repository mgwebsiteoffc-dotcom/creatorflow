<x-layouts.app panel="guest"
    title="Resources — playbooks, benchmarks, templates & videos for Indian brands"
    metaDescription="Free CreatorFlow resources — India-first playbooks, 2026 rate benchmarks, brief templates and product-tour videos for DTC brands and creators.">

    @include('marketing._hero', [
        'eyebrow' => 'Resources · Learn, ship, grow',
        'title'   => 'The <span class="text-gradient">creator marketing</span> library',
        'sub'     => 'Playbooks, templates, benchmarks and video walkthroughs — India-first, free forever.',
        'ctaText' => 'Browse resources',
        'ctaHref' => '#grid',
    ])

    <section id="grid" class="mx-auto max-w-6xl px-4 pb-16">
        {{-- Categories --}}
        <div class="grid gap-5 md:grid-cols-4">
            @foreach($categories as $slug => $c)
                @php $count = collect($items)->where('category', $slug)->count(); @endphp
                <a href="{{ route('resources.category', $slug) }}" class="card card-hover flex items-center gap-3 p-5">
                    <span class="grid h-11 w-11 shrink-0 place-items-center rounded-xl bg-gradient-to-br {{ $c['grad'] }} text-lg text-white">{{ $c['icon'] }}</span>
                    <div>
                        <div class="font-bold text-slate-900">{{ $c['label'] }}</div>
                        <div class="text-xs text-slate-500">{{ $count }} {{ \Illuminate\Support\Str::plural('item', $count) }}</div>
                    </div>
                </a>
            @endforeach
        </div>
    </section>

    {{-- Featured / all items --}}
    <section class="mx-auto max-w-6xl px-4 pb-16">
        <div class="flex items-end justify-between">
            <h2 class="section-title reveal">Featured resources</h2>
            <a href="{{ route('blog.index') }}" class="hidden text-sm font-semibold text-violet-700 hover:underline sm:inline">Read the blog →</a>
        </div>

        <div class="mt-8 grid gap-6 md:grid-cols-2 lg:grid-cols-3">
            @foreach($items as $r)
                @php $cat = $categories[$r['category']]; @endphp
                <a href="{{ route('resources.show', $r['slug']) }}" class="reveal card card-hover group flex flex-col overflow-hidden">
                    <div class="relative h-32 bg-gradient-to-br {{ $cat['grad'] }}">
                        <div class="absolute inset-0 opacity-25" style="background: radial-gradient(200px 200px at 30% 30%, rgba(255,255,255,.5), transparent 60%);"></div>
                        <span class="absolute left-4 top-4 rounded-full bg-white/25 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider text-white backdrop-blur">{{ $cat['label'] }}</span>
                        <span class="absolute right-4 top-4 text-2xl">{{ $cat['icon'] }}</span>
                    </div>
                    <div class="flex-1 p-5">
                        <h3 class="font-bold text-slate-900">{{ $r['title'] }}</h3>
                        <p class="mt-1 text-sm text-slate-600">{!! $r['summary'] !!}</p>
                    </div>
                    <div class="px-5 pb-5 text-sm font-semibold text-violet-700 group-hover:text-violet-900">Read · {{ $r['read_min'] }} min →</div>
                </a>
            @endforeach
        </div>
    </section>

    {{-- Newsletter --}}
    <section class="mx-auto max-w-6xl px-4 pb-24">
        <div class="reveal relative overflow-hidden rounded-3xl bg-slate-950 p-8 md:p-12">
            <div class="pointer-events-none absolute -right-10 -top-10 h-64 w-64 rounded-full bg-gradient-to-br from-violet-500/40 to-pink-500/30 blur-3xl"></div>
            <div class="grid gap-6 md:grid-cols-2 md:items-center">
                <div class="text-white">
                    <span class="chip !border-white/20 !bg-white/10 !text-white"><span class="chip-dot !bg-white"></span> Newsletter</span>
                    <h2 class="mt-4 text-3xl font-black">Get the CreatorFlow Weekly.</h2>
                    <p class="mt-2 text-white/80">One email, every Friday. Playbooks, benchmarks and the best campaigns of the week. Read in 5 min.</p>
                </div>
                <form onsubmit="event.preventDefault(); alert('Subscribed ✓')" class="flex flex-col gap-3 sm:flex-row">
                    <input type="email" required placeholder="you@brand.com" class="input flex-1 !bg-white/95">
                    <button class="btn-gradient">Subscribe →</button>
                </form>
            </div>
        </div>
    </section>
</x-layouts.app>
