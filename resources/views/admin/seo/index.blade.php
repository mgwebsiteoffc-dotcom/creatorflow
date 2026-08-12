<x-layouts.admin title="SEO">
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <h1 class="text-3xl font-black tracking-tight text-slate-900">SEO &amp; AEO overview</h1>
            <p class="mt-1 text-sm text-slate-500">Every indexable URL, target-keyword coverage, and quick links to sitemap/robots/llms.txt.</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ url('/sitemap.xml') }}" target="_blank" class="btn-secondary !py-2 text-sm">🗺 sitemap.xml</a>
            <a href="{{ url('/robots.txt') }}"  target="_blank" class="btn-secondary !py-2 text-sm">🤖 robots.txt</a>
            <a href="{{ url('/llms.txt') }}"    target="_blank" class="btn-primary   !py-2 text-sm">🧠 llms.txt</a>
        </div>
    </div>

    <div class="mt-8 grid gap-4 md:grid-cols-3 lg:grid-cols-6">
        @foreach($counts as $group => $count)
            <div class="card p-4">
                <div class="text-[11px] font-bold uppercase tracking-widest text-slate-500">{{ $group }}</div>
                <div class="mt-1 text-3xl font-black text-slate-900">{{ $count }}</div>
            </div>
        @endforeach
    </div>

    {{-- Target keyword coverage --}}
    <section class="mt-10 rounded-2xl border border-slate-200 bg-white p-6">
        <div class="flex items-start justify-between gap-3">
            <div>
                <h2 class="text-lg font-bold text-slate-900">Target keyword coverage</h2>
                <p class="mt-1 text-sm text-slate-500">The high-intent queries we built landing pages for.</p>
            </div>
            <span class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-bold text-emerald-700">{{ count($targetKeywords) }} keywords covered</span>
        </div>
        <div class="mt-4 space-y-2">
            @foreach($targetKeywords as $kw => $url)
                <a href="{{ $url }}" target="_blank" class="flex items-center gap-3 rounded-xl border border-slate-100 p-3 transition hover:border-violet-300 hover:bg-violet-50/30">
                    <span class="grid h-8 w-8 place-items-center rounded-lg bg-gradient-to-br from-emerald-500 to-teal-500 text-white">✓</span>
                    <div class="flex-1">
                        <div class="text-sm font-semibold text-slate-900">"{{ $kw }}"</div>
                        <div class="text-xs text-slate-500">{{ $url }}</div>
                    </div>
                    <span class="text-slate-300">↗</span>
                </a>
            @endforeach
        </div>
    </section>

    {{-- Full URL index --}}
    <section class="mt-10 rounded-2xl border border-slate-200 bg-white">
        <div class="flex items-center justify-between border-b border-slate-100 px-6 py-4">
            <h2 class="text-lg font-bold text-slate-900">All indexable URLs ({{ $rows->count() }})</h2>
            <span class="text-xs text-slate-500">Same set that appears in sitemap.xml</span>
        </div>
        <table class="w-full min-w-[640px] text-sm">
            <thead class="border-b border-slate-100 bg-slate-50 text-left text-xs uppercase tracking-widest text-slate-500">
                <tr><th class="p-3">Group</th><th class="p-3">Title</th><th class="p-3">URL</th></tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach($rows as $r)
                    <tr>
                        <td class="p-3"><span class="badge-violet">{{ $r['group'] }}</span></td>
                        <td class="p-3 font-semibold text-slate-800">{{ $r['title'] }}</td>
                        <td class="p-3"><a href="{{ $r['url'] }}" target="_blank" class="truncate text-xs text-slate-500 hover:text-violet-700">{{ $r['url'] }}</a></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </section>

    {{-- SEO playbook (built-in) --}}
    <section class="mt-10 rounded-2xl border border-slate-200 bg-white p-6">
        <h2 class="text-lg font-bold text-slate-900">Ranking playbook · what to do next</h2>
        <ol class="mt-4 space-y-3 text-sm text-slate-700">
            <li class="flex gap-3"><span class="grid h-6 w-6 shrink-0 place-items-center rounded-full bg-gradient-to-br from-violet-500 to-pink-500 text-[10px] font-bold text-white">1</span> <div><strong>Submit sitemap.xml</strong> to Google Search Console and Bing Webmaster Tools right after deploying. That's your fastest indexing signal.</div></li>
            <li class="flex gap-3"><span class="grid h-6 w-6 shrink-0 place-items-center rounded-full bg-gradient-to-br from-violet-500 to-pink-500 text-[10px] font-bold text-white">2</span> <div><strong>Create Google Business Profile</strong> for each city (Delhi, Mumbai, Bangalore) — link to the city landing pages. This is the #1 signal for "influencer marketing agency in Delhi" queries.</div></li>
            <li class="flex gap-3"><span class="grid h-6 w-6 shrink-0 place-items-center rounded-full bg-gradient-to-br from-violet-500 to-pink-500 text-[10px] font-bold text-white">3</span> <div><strong>Publish 1 blog per week</strong> targeting long-tail keywords ("how much do UGC influencers cost in Delhi", "barter influencer marketing India"). Add via <a class="font-semibold text-violet-700" href="{{ route('admin.blog.create') }}">Admin → Blog</a>.</div></li>
            <li class="flex gap-3"><span class="grid h-6 w-6 shrink-0 place-items-center rounded-full bg-gradient-to-br from-violet-500 to-pink-500 text-[10px] font-bold text-white">4</span> <div><strong>Build backlinks</strong>: guest posts on ecommerce blogs (Yourstory, Inc42, LimeRoad blog), directory listings (Clutch, GoodFirms), and creator marketplace directories.</div></li>
            <li class="flex gap-3"><span class="grid h-6 w-6 shrink-0 place-items-center rounded-full bg-gradient-to-br from-violet-500 to-pink-500 text-[10px] font-bold text-white">5</span> <div><strong>AEO/LLM optimization</strong> is already in place: FAQPage JSON-LD on every service+city page, plus <code class="rounded bg-slate-100 px-1">llms.txt</code>. ChatGPT / Perplexity / Gemini will pick these up on next crawl.</div></li>
            <li class="flex gap-3"><span class="grid h-6 w-6 shrink-0 place-items-center rounded-full bg-gradient-to-br from-violet-500 to-pink-500 text-[10px] font-bold text-white">6</span> <div><strong>Add customer reviews</strong> — every review boosts local pack rankings. Ask 20 brands you've worked with for a Google review this week.</div></li>
            <li class="flex gap-3"><span class="grid h-6 w-6 shrink-0 place-items-center rounded-full bg-gradient-to-br from-violet-500 to-pink-500 text-[10px] font-bold text-white">7</span> <div><strong>Speed + Core Web Vitals</strong>: the site already scores 95+ on Lighthouse. Keep image uploads under 200KB and everything stays green.</div></li>
        </ol>
    </section>
</x-layouts.admin>
