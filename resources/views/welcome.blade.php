<x-layouts.app panel="guest" title="The AI creator-commerce platform">
    <script type="application/ld+json">
    {!! json_encode([
        '@context' => 'https://schema.org',
        '@type'    => 'Organization',
        'name'     => 'CreatorFlow',
        'url'      => url('/'),
        'sameAs'   => [],
        'description' => 'AI-powered creator commerce platform for Shopify and any brand.',
    ], JSON_UNESCAPED_SLASHES) !!}
    </script>
    <script type="application/ld+json">
    {!! json_encode([
        '@context' => 'https://schema.org',
        '@type'    => 'WebSite',
        'name'     => 'CreatorFlow',
        'url'      => url('/'),
    ], JSON_UNESCAPED_SLASHES) !!}
    </script>

    {{-- ============================ HERO ============================ --}}
    <section class="relative overflow-hidden">
        <div class="aurora"></div>
        <div class="aurora-3"></div>
        <div class="dotted absolute inset-0 -z-10"></div>

        <div class="mx-auto grid max-w-6xl gap-10 px-4 pb-16 pt-16 md:grid-cols-12 md:gap-12 md:pt-24">
            <div class="md:col-span-7">
                <span class="chip reveal">
                    <span class="chip-dot"></span>
                    AI + Shopify + Web · One backend
                </span>

                <h1 class="reveal mt-5 text-4xl font-black leading-[1.05] tracking-tight text-slate-900 sm:text-5xl md:text-6xl">
                    Create influencer campaigns that
                    <span class="text-gradient">
                        <span class="rotator">
                            <ul>
                                <li>convert.</li>
                                <li>seed 100s.</li>
                                <li>go viral.</li>
                                <li>convert.</li>
                            </ul>
                        </span>
                    </span>
                </h1>

                <p class="reveal mt-5 max-w-xl text-lg text-slate-600">
                    CreatorFlow is a tech- and AI-powered creator marketing platform.
                    Launch campaigns in minutes, seed products in bulk, review content,
                    and attribute every sale — for Shopify stores or any brand.
                </p>

                <div class="reveal mt-8 flex flex-col items-start gap-3 sm:flex-row">
                    @auth
                        <a href="{{ auth()->user()->creator ? route('creator.dashboard') : route('brand.dashboard') }}" class="btn-gradient">
                            Go to dashboard
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" d="M5 12h14M13 6l6 6-6 6"/></svg>
                        </a>
                    @else
                        <a href="{{ route('register') }}" class="btn-gradient">
                            Start free — brand or creator
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" d="M5 12h14M13 6l6 6-6 6"/></svg>
                        </a>
                        <a href="{{ route('shopify.install') }}" class="btn-glass">
                            <svg class="h-4 w-4 text-emerald-500" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2l3 3 4 1-1 15-6 1-6-1L5 6l4-1 3-3z"/></svg>
                            Install on Shopify
                        </a>
                    @endauth
                </div>

                <div class="reveal mt-10 grid max-w-lg grid-cols-3 gap-6 border-t border-slate-200 pt-6">
                    <div>
                        <div class="text-2xl font-black text-slate-900">100K+</div>
                        <div class="text-xs uppercase tracking-wider text-slate-500">Creators</div>
                    </div>
                    <div>
                        <div class="text-2xl font-black text-slate-900">1,000+</div>
                        <div class="text-xs uppercase tracking-wider text-slate-500">Brands</div>
                    </div>
                    <div>
                        <div class="text-2xl font-black text-slate-900">4.9<span class="text-amber-500">★</span></div>
                        <div class="text-xs uppercase tracking-wider text-slate-500">Avg rating</div>
                    </div>
                </div>
            </div>

            {{-- Hero visual --}}
            <div class="reveal md:col-span-5">
                <div class="relative">
                    <div class="absolute -inset-6 -z-10 rounded-[2rem] bg-gradient-to-tr from-violet-300/40 via-pink-300/30 to-cyan-300/30 blur-2xl"></div>

                    <div class="g-border p-4 shadow-[0_30px_80px_-30px_rgba(15,23,42,.25)]">
                        {{-- Fake campaign card --}}
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="h-2.5 w-2.5 rounded-full bg-rose-400"></span>
                                <span class="h-2.5 w-2.5 rounded-full bg-amber-400"></span>
                                <span class="h-2.5 w-2.5 rounded-full bg-emerald-400"></span>
                            </div>
                            <span class="text-xs font-medium text-slate-400">campaign · live</span>
                        </div>

                        <div class="mt-4 rounded-2xl bg-slate-50 p-4">
                            <div class="flex items-center gap-3">
                                <div class="grid h-10 w-10 place-items-center rounded-xl text-white"
                                     style="background-image: linear-gradient(135deg,#7c3aed,#ec4899);">
                                    <span class="text-sm font-black">✨</span>
                                </div>
                                <div>
                                    <div class="text-sm font-semibold text-slate-900">Summer glow drop</div>
                                    <div class="text-xs text-slate-500">120 creators · UGC + Reels</div>
                                </div>
                                <span class="ml-auto badge badge-violet">AI brief</span>
                            </div>

                            <div class="mt-4 grid grid-cols-3 gap-2 text-center">
                                <div class="rounded-lg bg-white p-2">
                                    <div class="text-xs text-slate-400">Reach</div>
                                    <div class="text-sm font-bold text-slate-900">4.2M</div>
                                </div>
                                <div class="rounded-lg bg-white p-2">
                                    <div class="text-xs text-slate-400">Engaged</div>
                                    <div class="text-sm font-bold text-slate-900">318K</div>
                                </div>
                                <div class="rounded-lg bg-white p-2">
                                    <div class="text-xs text-slate-400">GMV</div>
                                    <div class="text-sm font-bold text-emerald-600">$126K</div>
                                </div>
                            </div>
                        </div>

                        {{-- Applicants stack --}}
                        <div class="mt-4 flex items-center justify-between rounded-2xl border border-slate-200 p-3">
                            <div class="flex items-center gap-2">
                                <div class="flex -space-x-2">
                                    @foreach(['#f472b6','#a78bfa','#22d3ee','#f59e0b'] as $c)
                                        <span class="grid h-7 w-7 place-items-center rounded-full text-[10px] font-bold text-white ring-2 ring-white"
                                              style="background:{{ $c }}">{{ substr('ABMR',$loop->index,1) }}</span>
                                    @endforeach
                                </div>
                                <span class="text-xs text-slate-600">+38 new applicants</span>
                            </div>
                            <span class="badge badge-green">98% match</span>
                        </div>

                        {{-- Content review --}}
                        <div class="mt-3 rounded-2xl border border-slate-200 p-3">
                            <div class="flex items-center justify-between">
                                <div class="text-xs font-semibold text-slate-700">Content · Reel_02.mp4</div>
                                <span class="badge badge-amber">Needs review</span>
                            </div>
                            <div class="mt-2 flex items-center gap-2">
                                <div class="h-2 flex-1 rounded-full bg-slate-100">
                                    <div class="h-2 rounded-full" style="width:72%; background-image:linear-gradient(90deg,#7c3aed,#ec4899);"></div>
                                </div>
                                <span class="text-xs font-semibold text-slate-500">AI 8.4/10</span>
                            </div>
                        </div>
                    </div>

                    {{-- Floating chips --}}
                    <div class="absolute -left-6 top-6 hidden animate-pulse rounded-2xl bg-white p-3 shadow-lg ring-1 ring-slate-100 md:block">
                        <div class="flex items-center gap-2 text-xs">
                            <span class="grid h-6 w-6 place-items-center rounded-lg bg-emerald-100 text-emerald-700">$</span>
                            <span class="font-semibold text-slate-800">+$1,240 attributed</span>
                        </div>
                    </div>
                    <div class="absolute -right-4 bottom-8 hidden rounded-2xl bg-white p-3 shadow-lg ring-1 ring-slate-100 md:block">
                        <div class="flex items-center gap-2 text-xs">
                            <span class="grid h-6 w-6 place-items-center rounded-lg bg-violet-100 text-violet-700">◎</span>
                            <span class="font-semibold text-slate-800">Shopify synced</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ============================ CLIENTS MARQUEE ============================ --}}
    <section class="border-y border-slate-200 bg-white/60 py-10">
        <div class="mx-auto max-w-6xl px-4">
            <div class="text-center">
                <p class="section-eyebrow">Our clients</p>
                <h2 class="mt-2 text-lg font-semibold text-slate-800">Trusted by 1,000+ modern brands</h2>
            </div>

            <div class="marquee mt-8">
                <div class="marquee-track">
                    @foreach(['Stadows','ABCD','Disano','Soapywise','Iva Lens','Atul Bakery','Evereve','Mithila','Luxotica','Samsara','Seven Seas','Perfume+'] as $brand)
                        <div class="flex shrink-0 items-center gap-2 text-2xl font-black tracking-tight text-slate-400">
                            <span class="h-6 w-6 rounded-md"
                                  style="background-image:linear-gradient(135deg,rgba(124,58,237,.55),rgba(236,72,153,.45));"></span>
                            {{ $brand }}
                        </div>
                    @endforeach
                    {{-- duplicate for seamless loop --}}
                    @foreach(['Stadows','ABCD','Disano','Soapywise','Iva Lens','Atul Bakery','Evereve','Mithila','Luxotica','Samsara','Seven Seas','Perfume+'] as $brand)
                        <div class="flex shrink-0 items-center gap-2 text-2xl font-black tracking-tight text-slate-400">
                            <span class="h-6 w-6 rounded-md"
                                  style="background-image:linear-gradient(135deg,rgba(124,58,237,.55),rgba(236,72,153,.45));"></span>
                            {{ $brand }}
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    {{-- ============================ CAMPAIGN TYPES ============================ --}}
    <section id="campaigns" class="mx-auto max-w-6xl px-4 py-20">
        <div class="mx-auto max-w-2xl text-center">
            <p class="section-eyebrow reveal">Type of campaigns</p>
            <h2 class="section-title reveal mt-3">Every collab format your brand needs</h2>
            <p class="section-sub reveal mt-3">
                From full-service seeding to self-serve barter — one platform, six proven playbooks.
            </p>
        </div>

        <div class="mt-12 grid gap-5 md:grid-cols-2 lg:grid-cols-3">
            @php
                $campaigns = [
                    ['emoji' => '⭐', 'title' => 'Product Review', 'desc' => 'Authentic reviews from creators who actually use the product. Build trust and drive awareness at scale.', 'tint' => 'from-amber-100 to-rose-100'],
                    ['emoji' => '📣', 'title' => 'Brand Awareness', 'desc' => 'Creator-led storytelling designed to leave a mark. Reach the right audience, at scale, in their feed.', 'tint' => 'from-violet-100 to-fuchsia-100'],
                    ['emoji' => '📍', 'title' => 'Store Visit', 'desc' => 'Invite creators IRL — capture real experiences, drive foot traffic, and win local word of mouth.', 'tint' => 'from-cyan-100 to-emerald-100'],
                    ['emoji' => '⚡', 'title' => 'Self-Managed', 'desc' => 'Instant access to verified creator data. Affordable, flexible, and completely under your control.', 'tint' => 'from-indigo-100 to-sky-100'],
                    ['emoji' => '🎁', 'title' => 'Barter / Seeding', 'desc' => 'Trade products for content. A cost-effective way to generate genuine UGC and reach at 10x the volume.', 'tint' => 'from-pink-100 to-orange-100'],
                    ['emoji' => '🎬', 'title' => 'Product Videoshoot', 'desc' => 'High-quality creator-produced video. Ready for ads, DTC pages, and channels — authentic and pro.', 'tint' => 'from-emerald-100 to-teal-100'],
                ];
            @endphp

            @foreach($campaigns as $c)
                <div class="reveal card card-hover group relative overflow-hidden p-6">
                    <div class="absolute -right-8 -top-8 h-28 w-28 rounded-full bg-gradient-to-br {{ $c['tint'] }} blur-2xl opacity-70 transition-transform duration-500 group-hover:scale-125"></div>
                    <div class="relative">
                        <div class="grid h-11 w-11 place-items-center rounded-xl bg-white text-xl shadow-sm ring-1 ring-slate-100">
                            {{ $c['emoji'] }}
                        </div>
                        <h3 class="mt-4 text-lg font-bold text-slate-900">{{ $c['title'] }}</h3>
                        <p class="mt-2 text-sm text-slate-600">{{ $c['desc'] }}</p>
                        <a href="{{ auth()->check() ? route('brand.campaigns.create') : route('register') }}"
                           class="mt-5 inline-flex items-center gap-1 text-sm font-semibold text-violet-700 hover:text-violet-900">
                            Explore
                            <svg class="h-4 w-4 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" d="M5 12h14M13 6l6 6-6 6"/></svg>
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    {{-- ============================ WHY CHOOSE US ============================ --}}
    <section id="why" class="relative overflow-hidden py-20">
        <div class="absolute inset-0 -z-10"
             style="background: linear-gradient(180deg, #fbfaff 0%, #ffffff 100%);"></div>

        <div class="mx-auto max-w-6xl px-4">
            <div class="grid gap-12 md:grid-cols-2 md:items-center">
                <div class="reveal">
                    <p class="section-eyebrow">Why choose us?</p>
                    <h2 class="section-title mt-3 text-left">Why CreatorFlow always works.</h2>
                    <p class="mt-4 text-slate-600">
                        We combine cutting-edge AI, a giant verified creator database, and human expertise —
                        so every campaign hits the right people with the right message.
                    </p>

                    <div class="mt-8 space-y-4">
                        @php
                            $reasons = [
                                ['title' => 'Reaching the right audience', 'desc' => '100K+ verified creators + AI matching to ensure your budget only touches people who convert.', 'grad' => 'from-violet-500 to-pink-500', 'icon' => '🎯'],
                                ['title' => 'Dedicated campaign manager', 'desc' => 'A real human owns strategy, execution and communication end-to-end. Zero hand-holding required.', 'grad' => 'from-cyan-500 to-emerald-500', 'icon' => '🧑‍💼'],
                                ['title' => 'Tech + AI + human expertise', 'desc' => 'Data-driven decisions augmented with taste. AI ideates, humans polish, results compound.', 'grad' => 'from-amber-500 to-rose-500', 'icon' => '🤖'],
                            ];
                        @endphp
                        @foreach($reasons as $r)
                            <div class="reveal flex gap-4">
                                <div class="grid h-11 w-11 shrink-0 place-items-center rounded-xl bg-gradient-to-br {{ $r['grad'] }} text-lg text-white shadow-lg">
                                    {{ $r['icon'] }}
                                </div>
                                <div>
                                    <h4 class="font-bold text-slate-900">{{ $r['title'] }}</h4>
                                    <p class="text-sm text-slate-600">{{ $r['desc'] }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Collage visual --}}
                <div class="reveal relative">
                    <div class="absolute -inset-4 -z-10 rounded-[2rem] bg-gradient-to-tr from-pink-200/50 via-violet-200/50 to-cyan-200/50 blur-2xl"></div>
                    <div class="grid grid-cols-6 grid-rows-6 gap-3">
                        <div class="col-span-4 row-span-3 overflow-hidden rounded-2xl bg-gradient-to-br from-violet-500 to-pink-500 p-5 text-white shadow-lg">
                            <div class="text-xs uppercase tracking-widest opacity-80">Live campaign</div>
                            <div class="mt-2 text-2xl font-black leading-tight">Beauty drop · 87 creators active</div>
                            <div class="mt-6 flex items-center gap-4 text-xs">
                                <div><div class="text-2xl font-black">4.2M</div><div class="opacity-80">reach</div></div>
                                <div><div class="text-2xl font-black">8.1%</div><div class="opacity-80">ER</div></div>
                                <div><div class="text-2xl font-black">$126K</div><div class="opacity-80">GMV</div></div>
                            </div>
                        </div>
                        <div class="col-span-2 row-span-3 rounded-2xl bg-slate-900 p-4 text-white shadow-lg">
                            <div class="text-xs opacity-70">AI review</div>
                            <div class="mt-4 text-4xl font-black text-gradient" style="background-image:linear-gradient(120deg,#22d3ee,#a78bfa,#f472b6);">9.2</div>
                            <div class="mt-2 text-xs opacity-70">Brand fit score</div>
                            <div class="mt-4 space-y-1.5">
                                <div class="h-1.5 rounded-full bg-white/10"><div class="h-1.5 w-4/5 rounded-full bg-emerald-400"></div></div>
                                <div class="h-1.5 rounded-full bg-white/10"><div class="h-1.5 w-3/5 rounded-full bg-cyan-400"></div></div>
                                <div class="h-1.5 rounded-full bg-white/10"><div class="h-1.5 w-2/3 rounded-full bg-fuchsia-400"></div></div>
                            </div>
                        </div>
                        <div class="col-span-2 row-span-3 rounded-2xl bg-white p-4 shadow-lg ring-1 ring-slate-100">
                            <div class="flex -space-x-2">
                                @foreach(['#f472b6','#a78bfa','#22d3ee','#f59e0b','#34d399'] as $c)
                                    <span class="h-8 w-8 rounded-full ring-2 ring-white" style="background:{{ $c }}"></span>
                                @endforeach
                            </div>
                            <div class="mt-3 text-xs font-semibold text-slate-500">Creator pool</div>
                            <div class="text-xl font-black text-slate-900">1,283 matched</div>
                        </div>
                        <div class="col-span-4 row-span-3 overflow-hidden rounded-2xl bg-gradient-to-br from-cyan-400 to-emerald-400 p-5 text-white shadow-lg">
                            <div class="text-xs uppercase tracking-widest opacity-80">Attribution</div>
                            <div class="mt-2 text-2xl font-black leading-tight">$18.40 avg ROAS</div>
                            <div class="mt-6 flex items-end gap-1.5">
                                @foreach([30,55,45,72,60,85,68,92,80] as $h)
                                    <div class="w-3 rounded-t bg-white/80" style="height: {{ $h }}px"></div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ============================ PLATFORM TABS ============================ --}}
    <section id="platform" class="mx-auto max-w-6xl px-4 py-20">
        <div class="mx-auto max-w-2xl text-center">
            <p class="section-eyebrow reveal">About our platform</p>
            <h2 class="section-title reveal mt-3">Powered by tech &amp; AI, out of the box</h2>
            <p class="section-sub reveal mt-3">
                Everything you need — creation, management, and analytics — in one modern surface.
            </p>
        </div>

        <div data-tabs class="reveal mt-10">
            <div class="mx-auto flex w-fit gap-1 rounded-full border border-slate-200 bg-white p-1 shadow-sm">
                <button data-tab="create" class="tab-pill">✨ Campaign Creation</button>
                <button data-tab="manage" class="tab-pill">🧩 Management</button>
                <button data-tab="dash"   class="tab-pill">📊 Dashboard</button>
            </div>

            <div class="mt-10 grid gap-8 md:grid-cols-12 md:items-center">
                {{-- Panel: Create --}}
                <div data-panel="create" class="contents">
                    <div class="reveal md:col-span-6">
                        <h3 class="text-2xl font-bold text-slate-900">Launch smarter campaigns in minutes</h3>
                        <p class="mt-3 text-slate-600">
                            Choose a campaign type, target with 12+ parameters, and get an AI-generated brief instantly.
                            Pay on the platform, no ping-pong with agencies.
                        </p>
                        <ul class="mt-6 space-y-3 text-sm">
                            @foreach(['Campaign types with AI-generated briefs', 'Target your niche with 12+ parameters', 'Instant quotation & payment on-platform'] as $li)
                                <li class="flex items-start gap-3">
                                    <span class="mt-0.5 grid h-5 w-5 shrink-0 place-items-center rounded-full bg-gradient-to-br from-violet-500 to-pink-500 text-[11px] font-black text-white">✓</span>
                                    <span class="text-slate-700">{{ $li }}</span>
                                </li>
                            @endforeach
                        </ul>
                        <a href="{{ route('register') }}" class="btn-gradient mt-8">Explore all features →</a>
                    </div>
                    <div class="reveal md:col-span-6">
                        <div class="g-border p-4 shadow-xl">
                            <div class="rounded-xl bg-slate-50 p-4">
                                <div class="text-xs font-semibold text-slate-500">New campaign · step 2/4</div>
                                <div class="mt-2 text-lg font-bold text-slate-900">Skincare · Product Review</div>
                                <div class="mt-4 grid grid-cols-2 gap-3">
                                    @foreach([['Niche', 'Beauty · Skincare'], ['Region', 'Tier 1 · IN'], ['Follower', '20K–150K'], ['Budget', '$8,000']] as $kv)
                                        <div class="rounded-lg bg-white p-3">
                                            <div class="text-[11px] uppercase tracking-wider text-slate-400">{{ $kv[0] }}</div>
                                            <div class="text-sm font-semibold text-slate-900">{{ $kv[1] }}</div>
                                        </div>
                                    @endforeach
                                </div>
                                <div class="mt-4 rounded-lg border border-dashed border-violet-300 bg-violet-50 p-3 text-xs text-violet-800">
                                    <span class="font-bold">AI:</span> matched <span class="font-bold">148 creators</span> · projected reach <span class="font-bold">1.6M</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Panel: Manage --}}
                <div data-panel="manage" class="contents hidden">
                    <div class="reveal md:col-span-6">
                        <h3 class="text-2xl font-bold text-slate-900">Stay in control — we handle the heavy lifting</h3>
                        <p class="mt-3 text-slate-600">
                            Review profiles, track deliverables, share access with your team.
                            From performance to billing, everything is one click away.
                        </p>
                        <ul class="mt-6 space-y-3 text-sm">
                            @foreach(['Accept / reject creator profiles', 'Approve content & request changes', 'Team access with roles'] as $li)
                                <li class="flex items-start gap-3">
                                    <span class="mt-0.5 grid h-5 w-5 shrink-0 place-items-center rounded-full bg-gradient-to-br from-cyan-500 to-emerald-500 text-[11px] font-black text-white">✓</span>
                                    <span class="text-slate-700">{{ $li }}</span>
                                </li>
                            @endforeach
                        </ul>
                        <a href="{{ route('register') }}" class="btn-gradient mt-8">Explore all features →</a>
                    </div>
                    <div class="reveal md:col-span-6">
                        <div class="g-border p-4 shadow-xl">
                            <div class="rounded-xl bg-slate-50 p-4">
                                <div class="flex items-center justify-between">
                                    <div class="text-sm font-bold text-slate-900">Applicants · 42 new</div>
                                    <span class="badge badge-violet">Sorted by match</span>
                                </div>
                                <div class="mt-3 space-y-2">
                                    @foreach([['@aria.k', 'Beauty', 92, '#f472b6'], ['@theovlog', 'Lifestyle', 88, '#a78bfa'], ['@nova.eats', 'Food', 84, '#22d3ee'], ['@mira.reels', 'Fashion', 81, '#f59e0b']] as $a)
                                        <div class="flex items-center gap-3 rounded-lg bg-white p-3">
                                            <span class="grid h-9 w-9 place-items-center rounded-full text-xs font-bold text-white" style="background:{{ $a[3] }}">{{ substr($a[0],1,2) }}</span>
                                            <div class="flex-1">
                                                <div class="text-sm font-semibold text-slate-900">{{ $a[0] }}</div>
                                                <div class="text-xs text-slate-500">{{ $a[1] }} · 62K followers</div>
                                            </div>
                                            <div class="text-right">
                                                <div class="text-sm font-bold text-emerald-600">{{ $a[2] }}%</div>
                                                <div class="text-[10px] uppercase text-slate-400">match</div>
                                            </div>
                                            <button class="btn-gradient !py-1.5 !text-xs">Invite</button>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Panel: Dashboard --}}
                <div data-panel="dash" class="contents hidden">
                    <div class="reveal md:col-span-6">
                        <h3 class="text-2xl font-bold text-slate-900">Your influencer marketing command center</h3>
                        <p class="mt-3 text-slate-600">
                            Track progress at a glance, manage billing, and get support — all in one dashboard.
                            Mobile-first PWAs for both brand and creator sides.
                        </p>
                        <ul class="mt-6 space-y-3 text-sm">
                            @foreach(['One dashboard for campaigns, content & payouts', 'Real-time attribution & ROAS', 'Built-in support at every step'] as $li)
                                <li class="flex items-start gap-3">
                                    <span class="mt-0.5 grid h-5 w-5 shrink-0 place-items-center rounded-full bg-gradient-to-br from-amber-500 to-rose-500 text-[11px] font-black text-white">✓</span>
                                    <span class="text-slate-700">{{ $li }}</span>
                                </li>
                            @endforeach
                        </ul>
                        <a href="{{ route('register') }}" class="btn-gradient mt-8">Explore all features →</a>
                    </div>
                    <div class="reveal md:col-span-6">
                        <div class="g-border p-4 shadow-xl">
                            <div class="grid grid-cols-2 gap-3">
                                <div class="rounded-xl bg-gradient-to-br from-violet-500 to-pink-500 p-4 text-white">
                                    <div class="text-xs uppercase tracking-widest opacity-80">Active campaigns</div>
                                    <div class="mt-1 text-3xl font-black">12</div>
                                </div>
                                <div class="rounded-xl bg-gradient-to-br from-cyan-400 to-emerald-400 p-4 text-white">
                                    <div class="text-xs uppercase tracking-widest opacity-80">This month GMV</div>
                                    <div class="mt-1 text-3xl font-black">$186K</div>
                                </div>
                                <div class="col-span-2 rounded-xl bg-slate-50 p-4">
                                    <div class="mb-3 flex items-center justify-between text-xs text-slate-500">
                                        <span>Last 14 days · Revenue</span>
                                        <span class="text-emerald-600">▲ 34%</span>
                                    </div>
                                    <div class="flex items-end gap-1">
                                        @foreach([22,30,26,42,38,55,48,60,52,68,74,66,82,90] as $h)
                                            <div class="w-4 rounded-t"
                                                 style="height: {{ $h }}px; background-image: linear-gradient(180deg, #a78bfa, #ec4899);"></div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ============================ SAMPLE WORK · REEL CAROUSEL ============================ --}}
    <section class="relative overflow-hidden py-20">
        <div class="absolute inset-0 -z-10"
             style="background: linear-gradient(180deg, #ffffff 0%, #fbfaff 100%);"></div>

        <div class="mx-auto max-w-6xl px-4">
            <div class="mx-auto flex max-w-6xl flex-col items-center gap-4 sm:flex-row sm:items-end sm:justify-between">
                <div class="max-w-2xl text-center sm:text-left">
                    <p class="section-eyebrow reveal">Sample work · reels</p>
                    <h2 class="section-title reveal mt-3 sm:text-left">Turning ideas into <span class="text-gradient">viral drops</span></h2>
                    <p class="reveal mt-3 text-slate-600">Real creator videos we've shipped for real brands. Swipe →</p>
                </div>
                <div class="reveal flex gap-2">
                    <button type="button" class="grid h-11 w-11 place-items-center rounded-full border border-slate-200 bg-white text-slate-700 shadow-sm transition hover:border-violet-300 hover:text-violet-700" data-reel-prev aria-label="Previous">←</button>
                    <button type="button" class="grid h-11 w-11 place-items-center rounded-full text-white shadow-md" style="background-image: linear-gradient(135deg,#7c3aed,#ec4899);" data-reel-next aria-label="Next">→</button>
                </div>
            </div>

            {{-- Carousel track (snap + drag/swipe) --}}
            <div class="reveal relative mt-10">
                <div data-reel class="flex snap-x snap-mandatory gap-4 overflow-x-auto pb-6 pt-2 [-webkit-overflow-scrolling:touch] [scrollbar-width:none] [&::-webkit-scrollbar]:hidden"
                     style="scroll-padding-left: 1rem;">
                    @php
                        $reels = [
                            ['Samsara Ghee',        'Food',        'from-amber-400 to-orange-500',  '2.4M views',  '@nova.eats'],
                            ['Luxotica Perfume',    'Cosmetics',   'from-fuchsia-400 to-pink-500',  '840K views',  '@aria.k'],
                            ['Seven Seas Travel',   'Travel',      'from-cyan-400 to-blue-500',     '1.1M views',  '@theovlog'],
                            ['Perfume+',            'Cosmetics',   'from-rose-400 to-red-500',      '620K views',  '@mira.reels'],
                            ['Atul Bakery',         'Store Visit', 'from-yellow-400 to-amber-500',  '410K views',  '@foodie.desi'],
                            ['Evereve Lifestyle',   'Lifestyle',   'from-violet-500 to-purple-600', '1.8M views',  '@zia.styles'],
                            ['Roving Mode',         'Fashion',     'from-indigo-500 to-violet-500', '960K views',  '@fashioncore'],
                            ['Iva Lens',            'Eyewear',     'from-emerald-400 to-teal-500',  '580K views',  '@techkai'],
                        ];
                    @endphp
                    @foreach($reels as $r)
                        <article class="group relative aspect-[9/16] w-[240px] shrink-0 snap-start overflow-hidden rounded-3xl shadow-lg sm:w-[260px] md:w-[280px]">
                            {{-- Fake reel visual --}}
                            <div class="absolute inset-0 bg-gradient-to-br {{ $r[2] }}"></div>
                            <div class="absolute inset-0 opacity-40" style="background: radial-gradient(200px 200px at 30% 30%, rgba(255,255,255,.6), transparent 60%);"></div>

                            {{-- Fake UI chrome --}}
                            <div class="absolute inset-x-3 top-3 flex items-center justify-between text-white">
                                <span class="rounded-full bg-black/40 px-2 py-0.5 text-[10px] font-bold uppercase tracking-widest backdrop-blur">{{ $r[1] }}</span>
                                <span class="rounded-full bg-black/40 px-2 py-0.5 text-[10px] font-semibold backdrop-blur">0:{{ str_pad(15 + $loop->index * 3, 2, '0', STR_PAD_LEFT) }}</span>
                            </div>

                            {{-- Fake right-side action rail --}}
                            <div class="absolute right-3 bottom-16 flex flex-col items-center gap-3 text-white">
                                <div class="grid h-8 w-8 place-items-center rounded-full bg-white/20 backdrop-blur">❤</div>
                                <div class="grid h-8 w-8 place-items-center rounded-full bg-white/20 backdrop-blur">💬</div>
                                <div class="grid h-8 w-8 place-items-center rounded-full bg-white/20 backdrop-blur">↗</div>
                            </div>

                            {{-- Bottom overlay --}}
                            <div class="absolute inset-x-0 bottom-0 bg-gradient-to-t from-black/80 via-black/40 to-transparent p-4 pt-14 text-white">
                                <div class="text-[11px] font-semibold opacity-90">{{ $r[4] }}</div>
                                <div class="mt-0.5 text-lg font-black leading-tight">{{ $r[0] }}</div>
                                <div class="mt-1 flex items-center gap-2 text-[11px] opacity-90">
                                    <span>▶ {{ $r[3] }}</span>
                                </div>
                            </div>

                            {{-- Play button --}}
                            <div class="pointer-events-none absolute inset-0 flex items-center justify-center opacity-0 transition group-hover:opacity-100">
                                <div class="grid h-14 w-14 place-items-center rounded-full bg-white/30 text-2xl text-white backdrop-blur">▶</div>
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    {{-- ============================ APP SCREENSHOTS ============================ --}}
    <section id="screens" class="relative overflow-hidden py-20">
        <div class="absolute inset-0 -z-10 bg-gradient-to-b from-slate-50 to-white"></div>
        <div class="mx-auto max-w-6xl px-4">
            <div class="mx-auto max-w-2xl text-center">
                <p class="section-eyebrow reveal">Features in action</p>
                <h2 class="section-title reveal mt-3">One dashboard. <span class="text-gradient">Every campaign lever.</span></h2>
                <p class="section-sub reveal mt-3">Peek at the actual CreatorFlow surfaces you'll be shipping campaigns from.</p>
            </div>

            @php
                $screens = [
                    [
                        'title' => 'Campaign management',
                        'body'  => 'Review applicants, track deliverables, share access with your team. Approvals in one click.',
                        'bullets' => ['Accept / reject applications inline', 'Bulk seed 100s of creators', 'Team roles with granular access'],
                    ],
                    [
                        'title' => 'Creator marketplace',
                        'body'  => '100K+ verified creators. Filter by niche, region, follower count, engagement — invite instantly.',
                        'bullets' => ['12+ filters incl. audience overlap', 'AI match score per creator', 'Save shortlists for later'],
                    ],
                    [
                        'title' => 'Analytics & attribution',
                        'body'  => 'Every rupee attributed to the creator who drove it. Codes, UTM, referral links — all rolled up.',
                        'bullets' => ['Live ROAS by campaign', 'Per-creator revenue and CVR', 'Export CSV for finance'],
                    ],
                ];
            @endphp

            <div class="mt-14 space-y-24">
                @foreach($screens as $i => $s)
                    <div class="grid gap-10 md:grid-cols-2 md:items-center {{ $i % 2 === 1 ? 'md:[&>*:first-child]:order-last' : '' }}">
                        <div class="reveal">
                            <span class="section-eyebrow">Screenshot · #{{ $i + 1 }}</span>
                            <h3 class="section-title mt-3 text-left">{{ $s['title'] }}</h3>
                            <p class="mt-4 text-slate-600">{{ $s['body'] }}</p>
                            <ul class="mt-6 space-y-3 text-sm">
                                @foreach($s['bullets'] as $b)
                                    <li class="flex items-start gap-3">
                                        <span class="mt-0.5 grid h-5 w-5 shrink-0 place-items-center rounded-full bg-gradient-to-br from-violet-500 to-pink-500 text-[11px] font-black text-white">✓</span>
                                        <span class="text-slate-700">{{ $b }}</span>
                                    </li>
                                @endforeach
                            </ul>
                            <a href="{{ route('features') }}" class="btn-gradient mt-8">Explore all features →</a>
                        </div>

                        {{-- Fake dashboard screenshot --}}
                        <div class="reveal">
                            @include('marketing._app-screenshot', ['variant' => $i])
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ============================ CREATORS ============================ --}}
    <section id="creators" class="mx-auto max-w-6xl px-4 py-20">
        <div class="grid gap-10 md:grid-cols-12 md:items-end">
            <div class="md:col-span-6">
                <p class="section-eyebrow reveal">Real people. Real influence.</p>
                <h2 class="section-title reveal mt-3 text-left">Discover the faces behind impactful stories</h2>
                <p class="reveal mt-3 max-w-lg text-slate-600">
                    We connect you with verified creators across fashion, lifestyle, tech, food, and more.
                    Browse profiles, review reach, collaborate with the ones who actually fit your brand.
                </p>
            </div>
            <div class="md:col-span-6 md:text-right">
                <a href="{{ route('register') }}" class="btn-glass">Explore all creators →</a>
            </div>
        </div>

        <div class="mt-10 grid gap-4 sm:grid-cols-2 md:grid-cols-3">
            @php
                $people = [
                    ['name' => 'Aria K.', 'tag' => 'Skincare Creator', 'note' => '620K on IG · avg 8.1% ER', 'grad' => 'from-pink-400 to-rose-500'],
                    ['name' => 'Theo V.', 'tag' => 'Lifestyle & Travel', 'note' => '1.2M on TikTok · viral hooks', 'grad' => 'from-violet-500 to-indigo-500'],
                    ['name' => 'Nova E.', 'tag' => 'Food Creator', 'note' => '340K on Reels · 12% ER', 'grad' => 'from-amber-400 to-orange-500'],
                    ['name' => 'Mira R.', 'tag' => 'Fashion Reels', 'note' => '780K on IG · trendsetter', 'grad' => 'from-fuchsia-500 to-pink-500'],
                    ['name' => 'Kai N.', 'tag' => 'Tech Reviewer', 'note' => '2.1M on YT · long-form king', 'grad' => 'from-cyan-500 to-blue-500'],
                    ['name' => 'Zia P.', 'tag' => 'Comedy · Sketch', 'note' => '3.4M on IG · virality specialist', 'grad' => 'from-emerald-500 to-teal-500'],
                ];
            @endphp
            @foreach($people as $p)
                <div class="reveal card card-hover overflow-hidden">
                    <div class="relative h-40 overflow-hidden">
                        <div class="absolute inset-0 bg-gradient-to-br {{ $p['grad'] }}"></div>
                        <div class="absolute inset-0 opacity-30"
                             style="background: radial-gradient(120px 120px at 30% 30%, rgba(255,255,255,.5), transparent 60%);"></div>
                        <span class="absolute right-3 top-3 rounded-full bg-white/25 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider text-white backdrop-blur">Verified ✓</span>
                    </div>
                    <div class="relative -mt-8 px-5 pb-5">
                        <div class="ring-grad grid h-16 w-16 place-items-center overflow-hidden rounded-2xl bg-white text-2xl font-black text-slate-800 ring-4 ring-white">
                            {{ substr($p['name'],0,1) }}
                        </div>
                        <div class="mt-3 flex items-center justify-between gap-2">
                            <div>
                                <div class="text-base font-bold text-slate-900">{{ $p['name'] }}</div>
                                <div class="text-xs font-semibold uppercase tracking-wider text-violet-600">{{ $p['tag'] }}</div>
                            </div>
                            <button class="btn-glass !px-3 !py-1.5 !text-xs">Invite</button>
                        </div>
                        <p class="mt-2 text-sm text-slate-600">{{ $p['note'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    {{-- ============================ TESTIMONIALS ============================ --}}
    <section class="relative overflow-hidden py-20">
        <div class="absolute inset-0 -z-10 bg-gradient-to-b from-white to-slate-50"></div>
        <div class="mx-auto max-w-6xl px-4">
            <div class="mx-auto max-w-2xl text-center">
                <p class="section-eyebrow reveal">Loved by brands</p>
                <h2 class="section-title reveal mt-3">What our customers say</h2>
            </div>

            <div class="mt-12 grid gap-6 md:grid-cols-3">
                @php
                    $quotes = [
                        ['q' => 'CreatorFlow ran our barter campaign end-to-end and the UGC boosted our perfume launch instantly.', 'name' => 'Luxotica', 'role' => 'Cosmetic brand', 'grad' => 'from-pink-500 to-rose-500'],
                        ['q' => 'Our brand awareness campaign gave us the right exposure in the education space and drove quality traffic.', 'name' => 'Mywbut', 'role' => 'EdTech platform', 'grad' => 'from-violet-500 to-indigo-500'],
                        ['q' => 'The product review campaign delivered authentic influencer content that built real trust for our fashion line.', 'name' => 'weRbangali', 'role' => 'Regional fashion brand', 'grad' => 'from-cyan-500 to-emerald-500'],
                    ];
                @endphp
                @foreach($quotes as $q)
                    <figure class="reveal card card-hover p-6">
                        <div class="flex items-center gap-1 text-amber-500">
                            @for($i=0;$i<5;$i++)★@endfor
                        </div>
                        <blockquote class="mt-4 text-slate-700">
                            “{{ $q['q'] }}”
                        </blockquote>
                        <figcaption class="mt-6 flex items-center gap-3">
                            <span class="grid h-10 w-10 place-items-center rounded-full bg-gradient-to-br {{ $q['grad'] }} text-white font-bold">
                                {{ substr($q['name'],0,1) }}
                            </span>
                            <div>
                                <div class="text-sm font-bold text-slate-900">{{ $q['name'] }}</div>
                                <div class="text-xs text-slate-500">{{ $q['role'] }}</div>
                            </div>
                        </figcaption>
                    </figure>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ============================ FINAL CTA ============================ --}}
    <section id="pricing" class="mx-auto max-w-6xl px-4 pb-20">
        <div class="reveal relative overflow-hidden rounded-3xl p-10 text-white md:p-16"
             style="background-image: linear-gradient(120deg, #0f172a 0%, #4c1d95 40%, #db2777 80%, #f59e0b 110%);">
            <div class="pointer-events-none absolute -right-20 -top-20 h-72 w-72 rounded-full bg-white/20 blur-3xl"></div>
            <div class="pointer-events-none absolute -bottom-24 -left-16 h-72 w-72 rounded-full bg-cyan-400/30 blur-3xl"></div>

            <div class="relative max-w-2xl">
                <span class="chip !border-white/20 !bg-white/10 !text-white">
                    <span class="chip-dot !bg-white"></span> Two doors · One product
                </span>
                <h2 class="mt-4 text-3xl font-black leading-tight md:text-5xl">
                    Ready to run creator campaigns that actually convert?
                </h2>
                <p class="mt-4 max-w-xl text-white/85">
                    Start free — the Shopify app and web brand panel share the same engine.
                    Creators join in one tap. No card, no lock-in.
                </p>
                <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                    <a href="{{ route('register') }}" class="btn-glass !text-slate-900">Create your account</a>
                    <a href="{{ route('shopify.install') }}" class="btn-gradient">
                        Install on Shopify
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" d="M5 12h14M13 6l6 6-6 6"/></svg>
                    </a>
                </div>
                <p class="mt-3 text-xs text-white/70">No card required · <code class="rounded bg-white/10 px-1.5 py-0.5">php artisan migrate:fresh --seed</code> for demo data</p>
            </div>
        </div>
    </section>
</x-layouts.app>
