<x-layouts.app panel="guest"
    title="Free influencer marketing tools for Indian DTC brands & creators"
    metaDescription="Free tools for influencer marketing in India — ROI calculator (₹), creator rate calculator, AI campaign brief generator. Built for Delhi, Mumbai, Bangalore brands. No signup.">

    {{-- Rich JSON-LD (ItemList of SoftwareApplication + FAQ) --}}
    @php
        $siteUrl = url('/');
        $toolsLd = [
            '@context' => 'https://schema.org',
            '@type'    => 'ItemList',
            'name'     => 'Free influencer marketing tools for Indian brands',
            'description' => 'Free tools that help DTC brands and creators plan influencer campaigns in India — ROI calculator, rate calculator, AI brief generator.',
            'itemListElement' => [
                ['@type' => 'ListItem', 'position' => 1, 'url' => route('tools.roi'),   'name' => 'Influencer Campaign ROI Calculator (INR)'],
                ['@type' => 'ListItem', 'position' => 2, 'url' => route('tools.rate'),  'name' => 'Creator Rate Calculator — India benchmarks'],
                ['@type' => 'ListItem', 'position' => 3, 'url' => route('tools.brief'), 'name' => 'AI Creator Brief Generator'],
            ],
        ];
        $breadcrumbLd = [
            '@context' => 'https://schema.org',
            '@type'    => 'BreadcrumbList',
            'itemListElement' => [
                ['@type' => 'ListItem', 'position' => 1, 'name' => 'CreatorPlex', 'item' => $siteUrl],
                ['@type' => 'ListItem', 'position' => 2, 'name' => 'Free tools', 'item' => route('tools.index')],
            ],
        ];
        $faqLd = [
            '@context' => 'https://schema.org',
            '@type'    => 'FAQPage',
            'mainEntity' => [
                ['@type' => 'Question', 'name' => 'Are these influencer marketing tools free?',
                 'acceptedAnswer' => ['@type' => 'Answer', 'text' => 'Yes. Every tool on this page — the ROI calculator, creator rate calculator and AI brief generator — is free, requires no signup and works in your browser. You keep every result.']],
                ['@type' => 'Question', 'name' => 'Do the calculators support Indian rupees and Indian creator benchmarks?',
                 'acceptedAnswer' => ['@type' => 'Answer', 'text' => 'Yes. All numbers default to Indian rupees with proper ₹1,84,200 grouping, and rate benchmarks include an India region multiplier priced from thousands of real Delhi, Mumbai, Bangalore, Hyderabad and Gurugram deals.']],
                ['@type' => 'Question', 'name' => 'Can I use these tools before I sign up on CreatorPlex?',
                 'acceptedAnswer' => ['@type' => 'Answer', 'text' => 'Absolutely. The tools are the fastest way to see how CreatorPlex thinks about creator marketing. When you are ready to run a campaign end-to-end — invitations, contracts, product seeding, attribution — start the free plan.']],
                ['@type' => 'Question', 'name' => 'Are the rate benchmarks accurate for micro and nano influencers in India?',
                 'acceptedAnswer' => ['@type' => 'Answer', 'text' => 'Yes. The rate calculator is anchored on CreatorPlex deal data across nano (1K–10K), micro (10K–100K), mid (100K–500K), macro (500K–1M) and mega (1M+) creators in every top Indian metro.']],
            ],
        ];
    @endphp
    <x-marketing.json-ld :blocks="[$breadcrumbLd, $toolsLd, $faqLd]" />

    {{-- Hero --}}
    <section class="relative overflow-hidden">
        <div class="aurora"></div>
        <div class="mx-auto max-w-6xl px-4 pt-14 md:pt-20">
            <p class="section-eyebrow">Free tools · No signup · India-first</p>
            <h1 class="mt-3 text-4xl font-black tracking-tight text-slate-900 sm:text-5xl md:text-6xl">
                Free <span class="text-gradient">influencer marketing</span> tools for<br class="hidden sm:block"> Indian DTC brands &amp; creators
            </h1>
            <p class="mt-4 max-w-2xl text-lg text-slate-600">
                Model campaign ROI in rupees, benchmark creator rates for Delhi / Mumbai / Bangalore, and generate a launch-ready creator brief in one click. Built by the CreatorPlex team.
            </p>
            <div class="mt-6 flex flex-wrap items-center gap-3">
                <a href="#grid" class="btn-primary">Try a tool</a>
                <a href="{{ route('register') }}" class="btn-glass">Or start free →</a>
            </div>
            <div class="mt-4 flex flex-wrap items-center gap-3 text-xs text-slate-500">
                <span class="inline-flex items-center gap-1"><span class="h-2 w-2 rounded-full bg-emerald-500"></span> 100% free</span>
                <span class="inline-flex items-center gap-1"><span class="h-2 w-2 rounded-full bg-violet-500"></span> Works in your browser</span>
                <span class="inline-flex items-center gap-1"><span class="h-2 w-2 rounded-full bg-pink-500"></span> ₹ INR benchmarks</span>
                <span class="inline-flex items-center gap-1"><span class="h-2 w-2 rounded-full bg-amber-500"></span> 12,000+ deals analysed</span>
            </div>
        </div>
    </section>

    {{-- Tools grid --}}
    <section id="grid" class="mx-auto max-w-6xl px-4 pb-16 pt-14">
        <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
            @php
                $tools = [
                    ['route' => 'tools.roi',   'icon' => '📊', 'title' => 'ROI Calculator (INR)',
                     'desc' => 'Model reach, engagements, orders and ROAS in rupees. Adjust creators, followers, ER, CVR, AOV and cost — see live results.',
                     'grad' => 'from-violet-500 to-pink-500', 'chip' => 'Most used'],
                    ['route' => 'tools.rate',  'icon' => '💸', 'title' => 'Creator Rate Calculator',
                     'desc' => 'What should a Delhi micro-creator charge for a Reel? A Mumbai nano for UGC? Fair range priced by niche + region + engagement rate.',
                     'grad' => 'from-cyan-500 to-emerald-500', 'chip' => 'India benchmarks'],
                    ['route' => 'tools.brief', 'icon' => '📝', 'title' => 'AI Brief Generator',
                     'desc' => 'Paste your product. Get a launch-ready creator brief with hooks, shot list, dos/donts, deliverables and CTA — in one click.',
                     'grad' => 'from-amber-500 to-rose-500', 'chip' => 'AI'],
                    ['route' => 'tools.roi',   'icon' => '🎯', 'title' => 'Audience Overlap',
                     'desc' => 'Coming soon — spot which creators share audiences before you spend so you don\'t double-pay for the same followers.',
                     'grad' => 'from-indigo-500 to-violet-500', 'chip' => 'Soon'],
                    ['route' => 'tools.roi',   'icon' => '📅', 'title' => 'Content Calendar',
                     'desc' => 'Coming soon — plan drops and creator collabs across a 12-week grid with UTM presets and reminders.',
                     'grad' => 'from-emerald-500 to-teal-500', 'chip' => 'Soon'],
                    ['route' => 'tools.brief', 'icon' => '🎬', 'title' => 'Hook Generator',
                     'desc' => 'Coming soon — 20 scroll-stopping Reel and TikTok hooks for your product in five seconds. Perfect for creative teams.',
                     'grad' => 'from-fuchsia-500 to-purple-600', 'chip' => 'Soon'],
                ];
            @endphp
            @foreach($tools as $t)
                <a href="{{ route($t['route']) }}" class="reveal card card-hover group relative flex flex-col overflow-hidden p-6">
                    <div class="absolute -right-8 -top-8 h-28 w-28 rounded-full bg-gradient-to-br {{ $t['grad'] }} opacity-25 blur-2xl transition-transform duration-500 group-hover:scale-125"></div>
                    <div class="relative">
                        <div class="flex items-center justify-between">
                            <span class="grid h-11 w-11 place-items-center rounded-xl bg-gradient-to-br {{ $t['grad'] }} text-lg text-white shadow-sm">{{ $t['icon'] }}</span>
                            <span class="badge badge-violet">{{ $t['chip'] }}</span>
                        </div>
                        <h3 class="mt-4 text-lg font-bold text-slate-900">{{ $t['title'] }}</h3>
                        <p class="mt-2 text-sm text-slate-600">{{ $t['desc'] }}</p>
                        <span class="mt-4 inline-flex items-center gap-1 text-sm font-semibold text-violet-700 group-hover:text-violet-900">
                            Open tool
                            <svg class="h-4 w-4 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" d="M5 12h14M13 6l6 6-6 6"/></svg>
                        </span>
                    </div>
                </a>
            @endforeach
        </div>
    </section>

    {{-- SEO / GEO body content --}}
    <section class="mx-auto max-w-4xl px-4 pb-16">
        <div class="prose prose-slate max-w-none">
            <h2 class="text-3xl font-black tracking-tight text-slate-900">Why Indian brands and creators trust these free tools</h2>
            <p class="text-slate-600">
                Every tool on this page is priced, benchmarked and tuned for the Indian influencer marketing market — Delhi NCR, Mumbai, Bangalore, Hyderabad, Chennai, Pune, Gurugram, Kolkata, Ahmedabad and Jaipur. The rate calculator sits on top of thousands of real barter and paid deals we've analysed on CreatorPlex. The ROI model uses assumptions that match how Shopify DTC brands in India actually convert. The AI brief generator produces briefs that read like a real growth marketer wrote them — not a lorem-ipsum template.
            </p>

            <h3 class="mt-8">Who these tools are for</h3>
            <ul class="text-slate-600">
                <li><strong>DTC founders in India</strong> planning their first (or 100th) influencer campaign — beauty, fashion, food, home, wellness, tech.</li>
                <li><strong>Performance marketers</strong> who want a defensible ROI story before pitching influencer spend to the founder.</li>
                <li><strong>Creators in Delhi, Mumbai, Bangalore</strong> and other metros who want a fair rate card before their next negotiation.</li>
                <li><strong>Agencies</strong> preparing decks for retainer clients and want city-level benchmarks with credibility.</li>
            </ul>

            <h3 class="mt-8">How to use these tools together</h3>
            <ol class="text-slate-600">
                <li><strong>Start with the ROI calculator (₹)</strong> — pick the number of creators and rough follower size, get expected reach, orders and revenue in Indian rupees.</li>
                <li><strong>Cross-check with the rate calculator</strong> — feed the same follower count + engagement rate + niche + region ("India") to see fair market rate per Reel, UGC video or full YouTube integration.</li>
                <li><strong>Generate a brief</strong> — describe your product in one line, pick niche + format + tone, and paste the output straight into your CreatorPlex campaign (or WhatsApp it to a creator).</li>
            </ol>

            <h3 class="mt-8">Indian creator marketing rate benchmarks (2026)</h3>
            <p class="text-slate-600">Rough ballpark ranges from CreatorPlex deals in the last 90 days across Delhi NCR, Mumbai and Bangalore:</p>
            <div class="not-prose mt-4 overflow-x-auto rounded-2xl border border-slate-200">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">
                        <tr><th class="px-4 py-3">Tier</th><th class="px-4 py-3">Followers</th><th class="px-4 py-3">UGC (1 photo)</th><th class="px-4 py-3">Reel / TikTok</th><th class="px-4 py-3">YouTube integration</th></tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <tr><td class="px-4 py-3 font-semibold">Nano</td>   <td class="px-4 py-3">1K – 10K</td>     <td class="px-4 py-3">₹0 – ₹2,500</td>       <td class="px-4 py-3">₹1,500 – ₹6,000</td>       <td class="px-4 py-3">₹3,000 – ₹10,000</td></tr>
                        <tr><td class="px-4 py-3 font-semibold">Micro</td>  <td class="px-4 py-3">10K – 100K</td>   <td class="px-4 py-3">₹1,500 – ₹8,000</td>    <td class="px-4 py-3">₹5,000 – ₹40,000</td>      <td class="px-4 py-3">₹15,000 – ₹90,000</td></tr>
                        <tr><td class="px-4 py-3 font-semibold">Mid</td>    <td class="px-4 py-3">100K – 500K</td>  <td class="px-4 py-3">₹6,000 – ₹35,000</td>   <td class="px-4 py-3">₹30,000 – ₹1,50,000</td>   <td class="px-4 py-3">₹80,000 – ₹4,00,000</td></tr>
                        <tr><td class="px-4 py-3 font-semibold">Macro</td>  <td class="px-4 py-3">500K – 1M</td>    <td class="px-4 py-3">₹25,000 – ₹80,000</td>  <td class="px-4 py-3">₹1,00,000 – ₹4,00,000</td> <td class="px-4 py-3">₹3,00,000 – ₹10,00,000</td></tr>
                        <tr><td class="px-4 py-3 font-semibold">Mega</td>   <td class="px-4 py-3">1M+</td>          <td class="px-4 py-3">₹60,000+</td>           <td class="px-4 py-3">₹3,00,000+</td>            <td class="px-4 py-3">₹8,00,000+</td></tr>
                    </tbody>
                </table>
            </div>
            <p class="mt-3 text-xs text-slate-500">Ranges are ±25% — always confirm with the rate calculator using the creator's actual engagement rate and niche.</p>

            <h3 class="mt-10">Frequently asked questions</h3>
            <div class="mt-4 space-y-3">
                @foreach($faqLd['mainEntity'] as $q)
                    <details class="rounded-xl border border-slate-200 bg-white p-4">
                        <summary class="cursor-pointer font-semibold text-slate-900">{{ $q['name'] }}</summary>
                        <p class="mt-2 text-slate-600">{{ $q['acceptedAnswer']['text'] }}</p>
                    </details>
                @endforeach
            </div>
        </div>
    </section>

    @include('marketing._cta', ['title' => 'Want these tools inside your workflow?', 'sub' => 'CreatorPlex bakes ROI modelling, rate benchmarks, and AI briefs into a full campaign engine. Free to start.'])
</x-layouts.app>
