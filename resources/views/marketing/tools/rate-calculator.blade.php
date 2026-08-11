<x-layouts.app panel="guest" title="Creator rate calculator">
    <section class="relative overflow-hidden">
        <div class="aurora"></div>
        <div class="mx-auto max-w-6xl px-4 pb-16 pt-14 md:pt-20">
            <nav class="text-xs text-slate-500">
                <a href="{{ route('tools.index') }}" class="hover:text-slate-900">Tools</a> → <span>Creator rate calculator</span>
            </nav>
            <h1 class="mt-3 text-4xl font-black tracking-tight text-slate-900 sm:text-5xl">
                Creator <span class="text-gradient">rate calculator</span>
            </h1>
            <p class="mt-3 max-w-2xl text-slate-600">Benchmarked from 12,000+ CreatorFlow deals. Enter followers, ER, and niche — get fair rates for UGC, Reels, and full videos.</p>
        </div>
    </section>

    <section class="mx-auto max-w-6xl px-4 pb-24">
        <div class="grid gap-8 md:grid-cols-5">
            <div class="md:col-span-2">
                <div class="g-border p-1 shadow-xl">
                    <div class="rounded-[calc(1.25rem-1px)] bg-white p-6 space-y-5">
                        <div>
                            <label class="label">Followers</label>
                            <input id="rc-followers" class="input" type="number" value="50000" min="1000">
                        </div>
                        <div>
                            <label class="label">Engagement rate %</label>
                            <input id="rc-er" class="input" type="number" step="0.1" value="5" min="0.1" max="30">
                        </div>
                        <div>
                            <label class="label">Niche</label>
                            <select id="rc-niche" class="input">
                                <option value="1.0">Lifestyle</option>
                                <option value="1.2">Beauty &amp; Skincare</option>
                                <option value="1.1">Fashion</option>
                                <option value="1.15">Food</option>
                                <option value="0.95">Travel</option>
                                <option value="1.25">Tech</option>
                                <option value="1.1">Fitness</option>
                                <option value="0.9">Comedy</option>
                                <option value="0.85">Gaming</option>
                            </select>
                        </div>
                        <div>
                            <label class="label">Region</label>
                            <select id="rc-region" class="input">
                                <option value="1.0">Global average</option>
                                <option value="1.4">US / UK / AU</option>
                                <option value="1.2">EU</option>
                                <option value="0.6">India</option>
                                <option value="0.7">LATAM</option>
                                <option value="0.8">SEA</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div class="md:col-span-3 space-y-4">
                @foreach([
                    ['ugc',   'UGC · 1 photo + caption', 'from-violet-500 to-pink-500', 0.010],
                    ['reel',  'Instagram Reel',           'from-cyan-500 to-emerald-500', 0.025],
                    ['tiktok','TikTok video',             'from-slate-800 to-slate-950', 0.028],
                    ['yt',    'YouTube integration (60s)','from-red-500 to-rose-600', 0.055],
                ] as $r)
                    <div class="rounded-2xl bg-gradient-to-br {{ $r[2] }} p-5 text-white shadow-lg" data-rate="{{ $r[3] }}" data-key="{{ $r[0] }}">
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="text-xs uppercase tracking-widest opacity-80">{{ $r[1] }}</div>
                                <div class="mt-1 text-3xl font-black" id="out-{{ $r[0] }}">$—</div>
                            </div>
                            <div class="text-right text-xs opacity-80">
                                <div>Fair range</div>
                                <div id="range-{{ $r[0] }}" class="font-semibold">—</div>
                            </div>
                        </div>
                    </div>
                @endforeach
                <div class="rounded-2xl border border-slate-200 bg-white p-5 text-xs text-slate-500">
                    <strong class="text-slate-700">How it works:</strong> Base rate = followers × CPM × niche mult × region mult × (1 + ER bonus). Fair range is ±25%. Use as a starting point in negotiation, not a ceiling.
                </div>
            </div>
        </div>
    </section>

    <script>
        (function() {
            const f = () => Number(document.getElementById('rc-followers').value) || 0;
            const er = () => Number(document.getElementById('rc-er').value) || 0;
            const nicheMult = () => Number(document.getElementById('rc-niche').value) || 1;
            const regionMult = () => Number(document.getElementById('rc-region').value) || 1;
            const $$ = (s) => document.querySelectorAll(s);
            const fmt$ = (n) => '$' + new Intl.NumberFormat('en-US', {maximumFractionDigits: 0}).format(Math.max(0, n));

            const calc = () => {
                const erBonus = Math.min(1.5, er() / 3); // ER of 3% baseline
                $$('[data-rate]').forEach((el) => {
                    const cpm = Number(el.dataset.rate);
                    const base = f() * cpm * nicheMult() * regionMult() * (1 + erBonus * 0.4);
                    const key = el.dataset.key;
                    document.getElementById('out-' + key).textContent = fmt$(base);
                    document.getElementById('range-' + key).textContent = fmt$(base * 0.75) + ' – ' + fmt$(base * 1.25);
                });
            };
            ['rc-followers','rc-er','rc-niche','rc-region'].forEach((id) => document.getElementById(id).addEventListener('input', calc));
            calc();
        })();
    </script>

    @include('marketing._cta', ['title' => 'Fair rates aren\'t enough. Automate the deal.', 'sub' => 'CreatorFlow handles contracts, payouts, and attribution in one flow.'])
</x-layouts.app>
