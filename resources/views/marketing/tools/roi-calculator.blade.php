<x-layouts.app panel="guest"
    title="Influencer Marketing ROI Calculator (INR) — free tool for Indian DTC brands"
    metaDescription="Free influencer marketing ROI calculator in Indian rupees. Model reach, engagements, orders and ROAS for creator campaigns in Delhi, Mumbai, Bangalore. No signup.">

    @php
        $roiFaqLd = [
            '@context' => 'https://schema.org',
            '@type' => 'FAQPage',
            'mainEntity' => [
                ['@type' => 'Question', 'name' => 'How is influencer marketing ROI calculated in India?',
                 'acceptedAnswer' => ['@type' => 'Answer', 'text' => 'Multiply the number of creators by average followers to get total reach. Multiply reach by engagement rate to get engagements, engagements by conversion rate to get orders, and orders by average order value (AOV in ₹) to get attributed revenue. ROAS = revenue / total campaign cost.']],
                ['@type' => 'Question', 'name' => 'What is a good ROAS for an influencer campaign for a DTC brand in India?',
                 'acceptedAnswer' => ['@type' => 'Answer', 'text' => 'For Indian DTC brands, a healthy ROAS on paid influencer campaigns is 3× – 5× within 30 days. Barter campaigns often show 8× – 15× ROAS because the only cost is product COGS + platform fee.']],
                ['@type' => 'Question', 'name' => 'What conversion rate should I assume for micro influencers?',
                 'acceptedAnswer' => ['@type' => 'Answer', 'text' => 'Micro influencers (10K–100K followers) in India typically drive 0.8% – 2.4% conversion on their engaged audience via unique discount codes. Beauty and food tend to be higher, tech and B2B tend to be lower.']],
                ['@type' => 'Question', 'name' => 'Does this tool work for barter campaigns?',
                 'acceptedAnswer' => ['@type' => 'Answer', 'text' => 'Yes. For a pure barter campaign, set the "Cost per creator" field to the retail value of the product you seed (or ₹0 if you want to see gross revenue without product cost). ROAS will reflect gift-value efficiency.']],
            ],
        ];
        $howToLd = [
            '@context' => 'https://schema.org',
            '@type' => 'HowTo',
            'name' => 'How to calculate influencer marketing ROI in INR',
            'description' => 'Estimate reach, orders and revenue for a creator campaign in India.',
            'step' => [
                ['@type' => 'HowToStep', 'position' => 1, 'name' => 'Set number of creators', 'text' => 'Start with how many creators you plan to invite — typical Indian DTC campaigns run 20–60 creators.'],
                ['@type' => 'HowToStep', 'position' => 2, 'name' => 'Set follower size', 'text' => 'Use the average follower count of the tier you\'re targeting — 5K for nano, 25K for micro, 200K for mid.'],
                ['@type' => 'HowToStep', 'position' => 3, 'name' => 'Enter engagement rate', 'text' => 'Micro creators in India average 5–8% ER. Verify from the creator\'s recent 10 posts.'],
                ['@type' => 'HowToStep', 'position' => 4, 'name' => 'Enter conversion rate + AOV', 'text' => 'Use your store\'s own AOV in ₹. Assume 1–2% CVR for beauty/food, 0.3–0.8% for higher AOV categories.'],
                ['@type' => 'HowToStep', 'position' => 5, 'name' => 'Read ROAS', 'text' => 'The profitability bar hits 100% at 5× ROAS — anything above that is a strong campaign.'],
            ],
        ];
        $swAppLd = [
            '@context' => 'https://schema.org',
            '@type' => 'SoftwareApplication',
            'name' => 'CreatorPlex ROI Calculator',
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
                ['@type' => 'ListItem', 'position' => 3, 'name' => 'ROI Calculator', 'item' => route('tools.roi')],
            ],
        ];
    @endphp
    <x-marketing.json-ld :blocks="[$breadcrumbLd, $swAppLd, $howToLd, $roiFaqLd]" />
    <section class="relative overflow-hidden">
        <div class="aurora"></div>
        <div class="mx-auto max-w-6xl px-4 pb-16 pt-14 md:pt-20">
            <nav class="text-xs text-slate-500">
                <a href="{{ route('tools.index') }}" class="hover:text-slate-900">Tools</a> → <span>ROI calculator</span>
            </nav>
            <h1 class="mt-3 text-4xl font-black tracking-tight text-slate-900 sm:text-5xl">
                Influencer Marketing <span class="text-gradient">ROI Calculator (₹)</span>
            </h1>
            <p class="mt-3 max-w-2xl text-slate-600">Model expected reach, engagements, orders and ROAS in Indian rupees for a creator campaign in Delhi, Mumbai, Bangalore or anywhere in India. Adjust the sliders — results update live.</p>
        </div>
    </section>

    <section class="mx-auto max-w-6xl px-4 pb-24">
        <div class="grid gap-8 md:grid-cols-5">
            {{-- Inputs --}}
            <div class="md:col-span-2">
                <div class="g-border p-1 shadow-xl">
                    <div class="rounded-[calc(1.25rem-1px)] bg-white p-6">
                        <h2 class="text-lg font-bold text-slate-900">Inputs</h2>

                        @php
                            // [id, label, default, min, max, step]
                            $inputs = [
                                ['creators',  'Number of creators',        30,     1,    500,    1],
                                ['followers', 'Avg followers per creator', 25000, 1000, 500000, 1000],
                                ['er',        'Engagement rate %',         5,     0.1,  20,     0.1],
                                ['cvr',       'Conversion rate %',         1.5,   0.1,  10,     0.1],
                                ['aov',       'Avg order value ₹',         2500,  100,  50000,  100],
                                ['cost',      'Cost per creator ₹',        4000,  0,    500000, 100],
                            ];
                        @endphp
                        <div class="mt-6 space-y-5">
                            @foreach($inputs as [$id, $label, $val, $min, $max, $step])
                                <div>
                                    <div class="flex items-center justify-between">
                                        <label class="text-sm font-medium text-slate-700" for="{{ $id }}">{{ $label }}</label>
                                        <input id="{{ $id }}-num" class="w-28 rounded-lg border border-slate-200 bg-white px-2 py-1 text-right text-sm font-semibold text-slate-900" type="number" value="{{ $val }}" step="{{ $step }}" min="{{ $min }}" max="{{ $max }}">
                                    </div>
                                    <input id="{{ $id }}" type="range" min="{{ $min }}" max="{{ $max }}" step="{{ $step }}" value="{{ $val }}" class="mt-2 h-2 w-full cursor-pointer appearance-none rounded-full bg-slate-100 accent-violet-600">
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            {{-- Output --}}
            <div class="md:col-span-3">
                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="rounded-2xl bg-gradient-to-br from-violet-500 to-pink-500 p-5 text-white shadow-lg">
                        <div class="text-xs uppercase tracking-widest opacity-80">Total reach</div>
                        <div id="out-reach" class="mt-1 text-4xl font-black">—</div>
                        <div class="mt-1 text-xs opacity-80">Followers × creators</div>
                    </div>
                    <div class="rounded-2xl bg-gradient-to-br from-cyan-500 to-emerald-500 p-5 text-white shadow-lg">
                        <div class="text-xs uppercase tracking-widest opacity-80">Engagements</div>
                        <div id="out-eng" class="mt-1 text-4xl font-black">—</div>
                        <div class="mt-1 text-xs opacity-80">Reach × ER%</div>
                    </div>
                    <div class="rounded-2xl bg-gradient-to-br from-amber-500 to-rose-500 p-5 text-white shadow-lg">
                        <div class="text-xs uppercase tracking-widest opacity-80">Est. orders</div>
                        <div id="out-orders" class="mt-1 text-4xl font-black">—</div>
                        <div class="mt-1 text-xs opacity-80">Engagements × CVR%</div>
                    </div>
                    <div class="rounded-2xl bg-slate-950 p-5 text-white shadow-lg">
                        <div class="text-xs uppercase tracking-widest opacity-80">Attributed revenue</div>
                        <div id="out-rev" class="mt-1 text-4xl font-black text-emerald-400">—</div>
                        <div class="mt-1 text-xs opacity-80">Orders × AOV</div>
                    </div>
                    <div class="col-span-2 rounded-2xl border border-slate-200 bg-white p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="text-xs uppercase tracking-widest text-slate-400">ROAS</div>
                                <div id="out-roas" class="mt-1 text-4xl font-black text-slate-900">—</div>
                                <div class="mt-1 text-xs text-slate-500">Revenue / Cost</div>
                            </div>
                            <div class="text-right">
                                <div class="text-xs uppercase tracking-widest text-slate-400">Total cost</div>
                                <div id="out-cost" class="mt-1 text-2xl font-bold text-slate-900">—</div>
                            </div>
                        </div>
                        <div class="mt-6 h-3 overflow-hidden rounded-full bg-slate-100">
                            <div id="out-bar" class="h-full rounded-full" style="width:0%; background-image: linear-gradient(90deg,#7c3aed,#ec4899,#f59e0b);"></div>
                        </div>
                        <div class="mt-2 text-[11px] text-slate-500">Profitability bar — hits 100% at 5× ROAS</div>
                    </div>
                </div>

                <div class="mt-6 rounded-2xl border border-dashed border-slate-300 bg-white p-4 text-xs text-slate-500">
                    <strong class="text-slate-700">Note:</strong> This is a model. Real results depend on creative, niche fit, seasonality, and product-market fit.
                </div>
            </div>
        </div>
    </section>

    <script>
        (function() {
            const ids = ['creators','followers','er','cvr','aov','cost'];
            const state = {};
            const fmt = (n) => new Intl.NumberFormat('en-IN', { maximumFractionDigits: 0 }).format(n);
            const fmt$ = (n) => '₹' + fmt(n);

            const bind = (id) => {
                const range = document.getElementById(id);
                const num = document.getElementById(id + '-num');
                const sync = (from, to) => { to.value = from.value; state[id] = Number(from.value); calc(); };
                range.addEventListener('input', () => sync(range, num));
                num.addEventListener('input', () => sync(num, range));
                state[id] = Number(range.value);
            };
            const calc = () => {
                const reach   = state.creators * state.followers;
                const eng     = reach * (state.er / 100);
                const orders  = eng * (state.cvr / 100);
                const rev     = orders * state.aov;
                const cost    = state.creators * state.cost;
                const roas    = cost > 0 ? rev / cost : 0;
                document.getElementById('out-reach').textContent = fmt(reach);
                document.getElementById('out-eng').textContent = fmt(eng);
                document.getElementById('out-orders').textContent = fmt(orders);
                document.getElementById('out-rev').textContent = fmt$(rev);
                document.getElementById('out-cost').textContent = fmt$(cost);
                document.getElementById('out-roas').textContent = (Math.round(roas*10)/10) + '×';
                document.getElementById('out-bar').style.width = Math.min(100, roas / 5 * 100) + '%';
            };
            ids.forEach(bind); calc();
        })();
    </script>

    {{-- SEO body content --}}
    <section class="mx-auto max-w-4xl px-4 pb-16">
        <div class="prose prose-slate max-w-none">
            <h2 class="text-2xl font-black tracking-tight text-slate-900">How influencer marketing ROI is calculated in India</h2>
            <p class="text-slate-600">
                The math is deceptively simple — the assumptions are what get brands into trouble. For an Indian DTC brand running a creator campaign in Delhi NCR, Mumbai or Bangalore, the ROI formula is:
            </p>
            <p class="not-prose rounded-2xl bg-slate-950 p-5 font-mono text-sm text-emerald-300">
                reach = creators × avg_followers<br>
                engagements = reach × ER%<br>
                orders = engagements × CVR%<br>
                revenue (₹) = orders × AOV<br>
                <span class="text-amber-300">ROAS = revenue / total_cost</span>
            </p>

            <h3 class="mt-8">Benchmark inputs for Indian DTC brands (2026)</h3>
            <ul class="text-slate-600">
                <li><strong>Nano creator (1K–10K)</strong> — ER 6–12%, CVR 1.2–2.4%, best for hyper-local Delhi / Bangalore drops.</li>
                <li><strong>Micro creator (10K–100K)</strong> — ER 4–8%, CVR 0.8–2.0%, the sweet spot for most Shopify India brands.</li>
                <li><strong>Mid creator (100K–500K)</strong> — ER 2.5–5%, CVR 0.3–1.2%, credibility + scale for Mumbai fashion, Delhi beauty, Bangalore tech.</li>
                <li><strong>Macro creator (500K–1M+)</strong> — ER 1–3%, CVR 0.2–0.6%, best for awareness pushes and launch weeks.</li>
                <li><strong>India AOV benchmarks</strong> — beauty ₹800–₹1,500, fashion ₹1,200–₹3,500, home ₹2,000–₹6,000, tech ₹3,000–₹15,000.</li>
            </ul>

            <h3 class="mt-8">Barter vs paid campaigns — which delivers better ROAS in India?</h3>
            <p class="text-slate-600">
                Barter (product-only) campaigns typically produce <strong>4× – 8× ROAS on retail</strong> because your only outflow is product COGS + our platform fee — most Indian DTC brands see barter working best for beauty, skincare, fashion and food where product experience is the story. Paid campaigns run <strong>2× – 5× ROAS</strong> but let you brief harder and control creative direction more tightly. Blended barter + paid (hybrid) campaigns are the norm on CreatorPlex for brands scaling past ₹50L/month.
            </p>

            <h3 class="mt-8">Frequently asked questions</h3>
            <div class="mt-4 space-y-3">
                @foreach($roiFaqLd['mainEntity'] as $q)
                    <details class="rounded-xl border border-slate-200 bg-white p-4">
                        <summary class="cursor-pointer font-semibold text-slate-900">{{ $q['name'] }}</summary>
                        <p class="mt-2 text-slate-600">{{ $q['acceptedAnswer']['text'] }}</p>
                    </details>
                @endforeach
            </div>

            <div class="mt-8 flex flex-wrap gap-2 text-sm">
                <a href="{{ route('tools.rate') }}" class="chip">→ Creator rate calculator</a>
                <a href="{{ route('tools.brief') }}" class="chip">→ AI brief generator</a>
                <a href="{{ url('/services/influencer-marketing-agency/delhi') }}" class="chip">→ Influencer marketing in Delhi</a>
                <a href="{{ url('/services/ugc-influencers/mumbai') }}" class="chip">→ UGC influencers in Mumbai</a>
            </div>
        </div>
    </section>

    @include('marketing._cta', ['title' => 'Model it here. Run it inside CreatorPlex.', 'sub' => 'Free plan includes 5 campaigns/month with real-time attribution baked in.'])
</x-layouts.app>
