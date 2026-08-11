<x-layouts.app panel="guest" title="Resources">
    @include('marketing._hero', [
        'eyebrow' => 'Resources · Learn, ship, grow',
        'title'   => 'The <span class="text-gradient">creator marketing</span> library',
        'sub'     => 'Playbooks, templates, benchmarks, and video walkthroughs. Free forever.',
        'ctaText' => 'Browse resources',
        'ctaHref' => '#grid',
    ])

    <section id="grid" class="mx-auto max-w-6xl px-4 pb-16">
        {{-- Categories --}}
        <div class="reveal grid gap-5 md:grid-cols-4">
            @foreach([
                ['📘', 'Playbooks',  '18 guides', 'from-violet-500 to-pink-500'],
                ['📊', 'Benchmarks', '2025 data', 'from-cyan-500 to-emerald-500'],
                ['🧾', 'Templates',  '32 assets', 'from-amber-500 to-rose-500'],
                ['🎥', 'Video',      '12 videos', 'from-indigo-500 to-violet-500'],
            ] as $c)
                <a href="#" class="card card-hover flex items-center gap-3 p-5">
                    <span class="grid h-11 w-11 shrink-0 place-items-center rounded-xl bg-gradient-to-br {{ $c[3] }} text-lg text-white">{{ $c[0] }}</span>
                    <div>
                        <div class="font-bold text-slate-900">{{ $c[1] }}</div>
                        <div class="text-xs text-slate-500">{{ $c[2] }}</div>
                    </div>
                </a>
            @endforeach
        </div>
    </section>

    {{-- Featured cards --}}
    <section class="mx-auto max-w-6xl px-4 pb-16">
        <h2 class="section-title reveal">Featured resources</h2>

        <div class="mt-8 grid gap-6 md:grid-cols-3">
            @foreach([
                ['guide', 'The DTC seeding playbook', '32-page PDF walking through your first 100 creators.', 'from-violet-500 to-pink-500'],
                ['spreadsheet', 'Creator rate benchmark 2025', 'CSV of 12K deal data points, sliced by niche and region.', 'from-cyan-500 to-emerald-500'],
                ['template', 'Brief template pack', '10 briefs by campaign type — copy, edit, ship.', 'from-amber-500 to-rose-500'],
                ['video', '15-min product tour', 'Everything CreatorFlow does, in a quarter of a coffee.', 'from-indigo-500 to-violet-500'],
                ['guide', 'Attribution 101', 'How to actually tie creators to revenue — no vanity metrics.', 'from-emerald-500 to-teal-500'],
                ['course', 'Barter mastery (free)', '6-part email course on running barter that converts.', 'from-fuchsia-500 to-purple-600'],
            ] as $r)
                <a href="#" class="reveal card card-hover group flex flex-col overflow-hidden">
                    <div class="relative h-32 bg-gradient-to-br {{ $r[3] }}">
                        <div class="absolute inset-0 opacity-25"
                             style="background: radial-gradient(200px 200px at 30% 30%, rgba(255,255,255,.5), transparent 60%);"></div>
                        <span class="absolute left-4 top-4 rounded-full bg-white/25 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider text-white backdrop-blur">{{ $r[0] }}</span>
                    </div>
                    <div class="flex-1 p-5">
                        <h3 class="font-bold text-slate-900">{{ $r[1] }}</h3>
                        <p class="mt-1 text-sm text-slate-600">{{ $r[2] }}</p>
                    </div>
                    <div class="px-5 pb-5 text-sm font-semibold text-violet-700 group-hover:text-violet-900">Read · Download →</div>
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
                    <p class="mt-2 text-white/80">One email, every Friday. Playbooks, benchmarks, and the best campaigns of the week. Read in 5 min.</p>
                </div>
                <form onsubmit="event.preventDefault(); alert('Subscribed ✓')" class="flex flex-col gap-3 sm:flex-row">
                    <input type="email" required placeholder="you@brand.com" class="input flex-1 !bg-white/95">
                    <button class="btn-gradient">Subscribe →</button>
                </form>
            </div>
        </div>
    </section>
</x-layouts.app>
