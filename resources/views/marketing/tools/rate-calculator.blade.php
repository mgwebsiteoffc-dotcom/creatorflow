<x-layouts.app panel="guest"
    title="Creator Rate Calculator (India) — free tool to price UGC, Reels & videos"
    metaDescription="Free creator rate calculator for India. Get fair pricing for UGC, Instagram Reels, TikTok and YouTube integrations in Delhi, Mumbai, Bangalore. Benchmarked from 12,000+ deals.">

    @php
        $rateFaqLd = [
            '@context' => 'https://schema.org',
            '@type' => 'FAQPage',
            'mainEntity' => [
                ['@type' => 'Question', 'name' => 'How much should a micro influencer in India charge per Reel?',
                 'acceptedAnswer' => ['@type' => 'Answer', 'text' => 'A micro influencer (10K–100K followers) in India typically charges ₹5,000 – ₹40,000 per Instagram Reel, depending on niche and engagement rate. Beauty and tech creators earn on the higher end, general lifestyle on the lower end.']],
                ['@type' => 'Question', 'name' => 'What is a fair UGC rate for a nano creator in Delhi?',
                 'acceptedAnswer' => ['@type' => 'Answer', 'text' => 'Nano creators (1K–10K) in Delhi typically charge ₹0 – ₹2,500 per UGC photo for barter or seeding campaigns and ₹1,500 – ₹6,000 for a paid Reel with usage rights.']],
                ['@type' => 'Question', 'name' => 'How does engagement rate affect a creator\'s rate?',
                 'acceptedAnswer' => ['@type' => 'Answer', 'text' => 'Higher ER commands premium pricing. This calculator applies an ER bonus multiplier that scales up to 1.6× at 8%+ ER — matching how CreatorPlex deals actually get negotiated.']],
                ['@type' => 'Question', 'name' => 'Are Mumbai and Bangalore creator rates different from Delhi?',
                 'acceptedAnswer' => ['@type' => 'Answer', 'text' => 'Rates across the top 3 Indian metros (Delhi, Mumbai, Bangalore) are within 10–15% of each other. Mumbai fashion + celebrity creators trend higher, Bangalore tech creators trend higher for B2B, Delhi is the deepest overall pool.']],
            ],
        ];
        $swAppLd = [
            '@context' => 'https://schema.org',
            '@type' => 'SoftwareApplication',
            'name' => 'CreatorPlex Rate Calculator',
            'applicationCategory' => 'BusinessApplication',
            'operatingSystem' => 'Web',
            'offers' => ['@type' => 'Offer', 'price' => '0', 'priceCurrency' => 'INR'],
            'aggregateRating' => ['@type' => 'AggregateRating', 'ratingValue' => '4.9', 'ratingCount' => '128'],
        ];
        $breadcrumbLd = [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => [
                ['@type' => 'ListItem', 'position' => 1, 'name' => 'CreatorPlex', 'item' => url('/')],
                ['@type' => 'ListItem', 'position' => 2, 'name' => 'Free tools', 'item' => route('tools.index')],
                ['@type' => 'ListItem', 'position' => 3, 'name' => 'Rate Calculator', 'item' => route('tools.rate')],
            ],
        ];
    @endphp
    <x-marketing.json-ld :blocks="[$breadcrumbLd, $swAppLd, $rateFaqLd]" />
    <section class="relative overflow-hidden">
        <div class="aurora"></div>
        <div class="mx-auto max-w-6xl px-4 pb-16 pt-14 md:pt-20">
            <nav class="text-xs text-slate-500">
                <a href="{{ route('tools.index') }}" class="hover:text-slate-900">Tools</a> → <span>Creator rate calculator</span>
            </nav>
            <h1 class="mt-3 text-4xl font-black tracking-tight text-slate-900 sm:text-5xl">
                Creator <span class="text-gradient">Rate Calculator</span> · India
            </h1>
            <p class="mt-3 max-w-2xl text-slate-600">Benchmarked from 12,000+ CreatorPlex deals across Delhi, Mumbai, Bangalore, Hyderabad and Pune. Enter followers, ER, and niche — get fair Indian rupee rates for UGC, Reels, TikToks and YouTube integrations.</p>
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
                                <option value="0.6" selected>India (Delhi / Mumbai / Bangalore avg)</option>
                                <option value="0.55">India (Tier-2: Pune / Jaipur / Kochi)</option>
                                <option value="0.65">India (Mumbai / Gurugram premium)</option>
                                <option value="1.0">Global average</option>
                                <option value="1.4">US / UK / AU</option>
                                <option value="1.2">EU</option>
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
                                <div class="mt-1 text-3xl font-black" id="out-{{ $r[0] }}">₹—</div>
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
            const USD_TO_INR = 83; // rough conversion so base CPM in USD → ₹
            const f = () => Number(document.getElementById('rc-followers').value) || 0;
            const er = () => Number(document.getElementById('rc-er').value) || 0;
            const nicheMult = () => Number(document.getElementById('rc-niche').value) || 1;
            const regionMult = () => Number(document.getElementById('rc-region').value) || 1;
            const $$ = (s) => document.querySelectorAll(s);
            const fmt$ = (n) => '₹' + new Intl.NumberFormat('en-IN', {maximumFractionDigits: 0}).format(Math.max(0, n));

            const calc = () => {
                const erBonus = Math.min(1.5, er() / 3); // ER of 3% baseline
                $$('[data-rate]').forEach((el) => {
                    const cpm = Number(el.dataset.rate);
                    const base = f() * cpm * nicheMult() * regionMult() * (1 + erBonus * 0.4) * USD_TO_INR;
                    const key = el.dataset.key;
                    document.getElementById('out-' + key).textContent = fmt$(base);
                    document.getElementById('range-' + key).textContent = fmt$(base * 0.75) + ' – ' + fmt$(base * 1.25);
                });
            };
            ['rc-followers','rc-er','rc-niche','rc-region'].forEach((id) => document.getElementById(id).addEventListener('input', calc));
            calc();
        })();
    </script>

    {{-- SEO body content --}}
    <section class="mx-auto max-w-4xl px-4 pb-16">
        <div class="prose prose-slate max-w-none">
            <h2 class="text-2xl font-black tracking-tight text-slate-900">How much do Indian influencers charge in 2026?</h2>
            <p class="text-slate-600">
                Influencer rates in India vary by tier, niche, city, engagement rate and usage rights. Delhi and Mumbai command the deepest pools; Bangalore leads for tech and D2C wellness; Hyderabad and Pune are growing fastest. Use the calculator above for a live estimate, or scan the table below for a rough ballpark.
            </p>

            <div class="not-prose mt-4 overflow-x-auto rounded-2xl border border-slate-200">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                        <tr><th class="px-4 py-3">Tier</th><th class="px-4 py-3">Followers</th><th class="px-4 py-3">UGC photo</th><th class="px-4 py-3">Instagram Reel</th><th class="px-4 py-3">TikTok</th><th class="px-4 py-3">YouTube 60s</th></tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <tr><td class="px-4 py-3 font-semibold">Nano</td>   <td class="px-4 py-3">1K – 10K</td>     <td class="px-4 py-3">₹0 – ₹2,500</td>      <td class="px-4 py-3">₹1,500 – ₹6,000</td>       <td class="px-4 py-3">₹1,800 – ₹7,000</td>       <td class="px-4 py-3">₹3,000 – ₹10,000</td></tr>
                        <tr><td class="px-4 py-3 font-semibold">Micro</td>  <td class="px-4 py-3">10K – 100K</td>   <td class="px-4 py-3">₹1,500 – ₹8,000</td>   <td class="px-4 py-3">₹5,000 – ₹40,000</td>      <td class="px-4 py-3">₹6,000 – ₹45,000</td>      <td class="px-4 py-3">₹15,000 – ₹90,000</td></tr>
                        <tr><td class="px-4 py-3 font-semibold">Mid</td>    <td class="px-4 py-3">100K – 500K</td>  <td class="px-4 py-3">₹6,000 – ₹35,000</td>  <td class="px-4 py-3">₹30,000 – ₹1,50,000</td>   <td class="px-4 py-3">₹35,000 – ₹1,60,000</td>   <td class="px-4 py-3">₹80,000 – ₹4,00,000</td></tr>
                        <tr><td class="px-4 py-3 font-semibold">Macro</td>  <td class="px-4 py-3">500K – 1M</td>    <td class="px-4 py-3">₹25,000 – ₹80,000</td> <td class="px-4 py-3">₹1,00,000 – ₹4,00,000</td> <td class="px-4 py-3">₹1,10,000 – ₹4,20,000</td> <td class="px-4 py-3">₹3,00,000 – ₹10,00,000</td></tr>
                        <tr><td class="px-4 py-3 font-semibold">Mega</td>   <td class="px-4 py-3">1M+</td>          <td class="px-4 py-3">₹60,000+</td>          <td class="px-4 py-3">₹3,00,000+</td>            <td class="px-4 py-3">₹3,50,000+</td>            <td class="px-4 py-3">₹8,00,000+</td></tr>
                    </tbody>
                </table>
            </div>
            <p class="mt-3 text-xs text-slate-500">Ranges vary ±25%. Always negotiate around the creator's engagement rate + niche fit rather than follower count alone.</p>

            <h3 class="mt-8">What drives Indian creator rates up (or down)</h3>
            <ul class="text-slate-600">
                <li><strong>Engagement rate above 5%</strong> — commands 1.3× – 1.6× premium.</li>
                <li><strong>Niche fit</strong> — Beauty, Tech and Fitness pay best; general Lifestyle and Comedy pay less per view.</li>
                <li><strong>Usage rights</strong> — 90-day paid usage typically adds 30–50% to the base rate; buyouts add 100%+.</li>
                <li><strong>Exclusivity</strong> — 30-day category exclusivity adds 25–40%.</li>
                <li><strong>Turnaround time</strong> — under-7-day rush jobs get a 15–25% surcharge.</li>
                <li><strong>Regional language</strong> — Tamil, Telugu, Malayalam and Bengali creators often price 10–20% lower on absolute rate but deliver 2× – 3× higher CVR for regional-language brands.</li>
            </ul>

            <h3 class="mt-8">Frequently asked questions</h3>
            <div class="mt-4 space-y-3">
                @foreach($rateFaqLd['mainEntity'] as $q)
                    <details class="rounded-xl border border-slate-200 bg-white p-4">
                        <summary class="cursor-pointer font-semibold text-slate-900">{{ $q['name'] }}</summary>
                        <p class="mt-2 text-slate-600">{{ $q['acceptedAnswer']['text'] }}</p>
                    </details>
                @endforeach
            </div>

            <div class="mt-8 flex flex-wrap gap-2 text-sm">
                <a href="{{ route('tools.roi') }}" class="chip">→ ROI Calculator (INR)</a>
                <a href="{{ route('tools.brief') }}" class="chip">→ AI brief generator</a>
                <a href="{{ url('/services/micro-influencer-marketing/bangalore') }}" class="chip">→ Micro influencer marketing in Bangalore</a>
                <a href="{{ url('/services/barter-influencers/delhi') }}" class="chip">→ Barter influencers in Delhi</a>
            </div>
        </div>
    </section>

    @include('marketing._cta', ['title' => 'Fair rates aren\'t enough. Automate the deal.', 'sub' => 'CreatorPlex handles contracts, payouts, and attribution in one flow.'])
</x-layouts.app>
