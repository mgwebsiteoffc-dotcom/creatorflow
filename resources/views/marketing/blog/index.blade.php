<x-layouts.app panel="guest" title="Blog">
    @include('marketing._hero', [
        'eyebrow' => 'CreatorPlex blog',
        'title'   => 'Insights from the <span class="text-gradient">creator commerce</span> frontline',
        'sub'     => 'Playbooks, benchmarks, and honest takes from the team building CreatorPlex — and the brands running on it.',
        'ctaText' => 'Subscribe',
        'ctaHref' => '#news',
    ])

    <section class="mx-auto max-w-6xl px-4 pb-16">
        {{-- Filter chips (decorative) --}}
        <div class="reveal mb-8 flex flex-wrap gap-2">
            @foreach(['All','Playbooks','Guides','Tactics','Benchmarks','Engineering','Templates'] as $tag)
                <button type="button" class="{{ $loop->first ? 'tab-pill is-active' : 'tab-pill' }}">{{ $tag }}</button>
            @endforeach
        </div>

        {{-- Featured (first post) --}}
        @php $featured = $posts[0]; $rest = array_slice($posts, 1); @endphp
        <a href="{{ route('blog.show', $featured['slug']) }}" class="reveal card card-hover group grid overflow-hidden md:grid-cols-2">
            <div class="relative h-64 bg-gradient-to-br {{ $featured['grad'] }} md:h-full">
                <div class="absolute inset-0 opacity-25" style="background: radial-gradient(300px 300px at 40% 30%, rgba(255,255,255,.55), transparent 60%);"></div>
                <span class="absolute left-4 top-4 rounded-full bg-white/25 px-3 py-1 text-xs font-bold uppercase tracking-wider text-white backdrop-blur">Featured · {{ $featured['category'] }}</span>
            </div>
            <div class="flex flex-col justify-center gap-3 p-6 md:p-10">
                <div class="text-xs font-semibold uppercase tracking-widest text-violet-700">{{ $featured['category'] }} · {{ $featured['read'] }} read</div>
                <h2 class="text-2xl font-black leading-tight text-slate-900 md:text-3xl">{{ $featured['title'] }}</h2>
                <p class="text-slate-600">{!! $featured['excerpt'] !!}</p>
                <div class="mt-2 flex items-center gap-3">
                    <span class="grid h-9 w-9 place-items-center rounded-full bg-gradient-to-br {{ $featured['grad'] }} text-sm font-bold text-white">{{ substr($featured['author'],0,1) }}</span>
                    <div class="text-xs">
                        <div class="font-bold text-slate-900">{{ $featured['author'] }}</div>
                        <div class="text-slate-500">{{ \Carbon\Carbon::parse($featured['date'])->format('M j, Y') }}</div>
                    </div>
                </div>
            </div>
        </a>

        {{-- Rest --}}
        <div class="mt-8 grid gap-6 md:grid-cols-3">
            @foreach($rest as $p)
                <a href="{{ route('blog.show', $p['slug']) }}" class="reveal card card-hover group flex flex-col overflow-hidden">
                    <div class="relative h-40 bg-gradient-to-br {{ $p['grad'] }}">
                        <div class="absolute inset-0 opacity-25" style="background: radial-gradient(200px 200px at 30% 30%, rgba(255,255,255,.55), transparent 60%);"></div>
                        <span class="absolute left-4 top-4 rounded-full bg-white/25 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider text-white backdrop-blur">{{ $p['category'] }}</span>
                    </div>
                    <div class="flex-1 p-5">
                        <h3 class="text-lg font-bold text-slate-900 group-hover:text-violet-700">{{ $p['title'] }}</h3>
                        <p class="mt-2 text-sm text-slate-600">{!! $p['excerpt'] !!}</p>
                    </div>
                    <div class="flex items-center justify-between border-t border-slate-100 px-5 py-3 text-xs text-slate-500">
                        <span class="font-semibold text-slate-700">{{ $p['author'] }}</span>
                        <span>{{ \Carbon\Carbon::parse($p['date'])->format('M j') }} · {{ $p['read'] }}</span>
                    </div>
                </a>
            @endforeach
        </div>
    </section>

    <section id="news" class="mx-auto max-w-6xl px-4 pb-24">
        <div class="reveal relative overflow-hidden rounded-3xl bg-slate-950 p-8 md:p-12">
            <div class="pointer-events-none absolute -right-10 -top-10 h-64 w-64 rounded-full bg-gradient-to-br from-violet-500/40 to-pink-500/30 blur-3xl"></div>
            <div class="grid gap-6 md:grid-cols-2 md:items-center">
                <div class="text-white">
                    <h2 class="text-3xl font-black">CreatorPlex Weekly</h2>
                    <p class="mt-2 text-white/80">One 5-minute read every Friday. Zero fluff.</p>
                </div>
                <form onsubmit="event.preventDefault(); alert('Subscribed ✓')" class="flex flex-col gap-3 sm:flex-row">
                    <input type="email" required placeholder="you@brand.com" class="input flex-1 !bg-white/95">
                    <button class="btn-gradient">Subscribe →</button>
                </form>
            </div>
        </div>
    </section>
</x-layouts.app>
