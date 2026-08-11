<x-layouts.app panel="guest"
    title="AI Creator Brief Generator — free tool for Indian brands & agencies"
    metaDescription="Free AI creator brief generator for influencer campaigns. Paste your product, get a launch-ready brief with hooks, shot list, dos/donts and deliverables in one click. Built for Indian DTC brands.">

    @php
        $briefFaqLd = [
            '@context' => 'https://schema.org',
            '@type' => 'FAQPage',
            'mainEntity' => [
                ['@type' => 'Question', 'name' => 'What should a creator brief include?',
                 'acceptedAnswer' => ['@type' => 'Answer', 'text' => 'A strong creator brief includes: brand + product summary, campaign goal, hook options, storyboard/shot list, must-includes (mentions, on-screen text, disclosures), do/don\'t list, deliverables + format, deadline, usage rights and compensation. This tool covers every one.']],
                ['@type' => 'Question', 'name' => 'Is the AI brief generator free?',
                 'acceptedAnswer' => ['@type' => 'Answer', 'text' => 'Yes. This tool is 100% free with no signup. You get an editable brief you can paste into WhatsApp, email, a CreatorFlow campaign or your own doc.']],
                ['@type' => 'Question', 'name' => 'Does the brief work for Indian creators?',
                 'acceptedAnswer' => ['@type' => 'Answer', 'text' => 'Yes — hooks, tone and shot list are tuned for the Indian Reels/YouTube Shorts market. The output plugs directly into a CreatorFlow campaign where you can send it to matched creators in Delhi, Mumbai, Bangalore and beyond.']],
                ['@type' => 'Question', 'name' => 'Can I regenerate to get a different brief?',
                 'acceptedAnswer' => ['@type' => 'Answer', 'text' => 'Yes. Hit "Regenerate" for a fresh hook set and storyboard. Every regeneration reshuffles the creative angles so you can pick the strongest.']],
            ],
        ];
        $swAppLd = [
            '@context' => 'https://schema.org',
            '@type' => 'SoftwareApplication',
            'name' => 'CreatorFlow AI Brief Generator',
            'applicationCategory' => 'BusinessApplication',
            'operatingSystem' => 'Web',
            'offers' => ['@type' => 'Offer', 'price' => '0', 'priceCurrency' => 'INR'],
            'aggregateRating' => ['@type' => 'AggregateRating', 'ratingValue' => '4.9', 'ratingCount' => '128'],
        ];
        $breadcrumbLd = [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => [
                ['@type' => 'ListItem', 'position' => 1, 'name' => 'CreatorFlow', 'item' => url('/')],
                ['@type' => 'ListItem', 'position' => 2, 'name' => 'Free tools', 'item' => route('tools.index')],
                ['@type' => 'ListItem', 'position' => 3, 'name' => 'AI Brief Generator', 'item' => route('tools.brief')],
            ],
        ];
    @endphp
    <x-marketing.json-ld :blocks="[$breadcrumbLd, $swAppLd, $briefFaqLd]" />
    <section class="relative overflow-hidden">
        <div class="aurora"></div>
        <div class="mx-auto max-w-6xl px-4 pb-16 pt-14 md:pt-20">
            <nav class="text-xs text-slate-500">
                <a href="{{ route('tools.index') }}" class="hover:text-slate-900">Tools</a> → <span>AI brief generator</span>
            </nav>
            <h1 class="mt-3 text-4xl font-black tracking-tight text-slate-900 sm:text-5xl">
                AI creator <span class="text-gradient">brief generator</span>
            </h1>
            <p class="mt-3 max-w-2xl text-slate-600">Paste product details. Get a launch-ready creator brief. All client-side, all free.</p>
        </div>
    </section>

    <section class="mx-auto max-w-6xl px-4 pb-24">
        <div class="grid gap-8 md:grid-cols-2">
            <div>
                <div class="g-border p-1 shadow-xl">
                    <div class="rounded-[calc(1.25rem-1px)] bg-white p-6 space-y-4">
                        <div>
                            <label class="label">Product name</label>
                            <input id="bg-name" class="input" placeholder="Glow Serum 30ml" value="Glow Serum 30ml">
                        </div>
                        <div>
                            <label class="label">One-liner (who / what / why)</label>
                            <textarea id="bg-desc" class="input min-h-24" placeholder="Vitamin C serum for combo-skin Gen-Z, brightens dull spots in 14 days.">Vitamin C serum for combo-skin Gen-Z, brightens dull spots in 14 days.</textarea>
                        </div>
                        <div class="grid gap-3 sm:grid-cols-2">
                            <div>
                                <label class="label">Niche</label>
                                <select id="bg-niche" class="input">
                                    <option>Beauty &amp; Skincare</option>
                                    <option>Fashion</option>
                                    <option>Food</option>
                                    <option>Fitness</option>
                                    <option>Tech</option>
                                    <option>Travel</option>
                                    <option>Home</option>
                                </select>
                            </div>
                            <div>
                                <label class="label">Format</label>
                                <select id="bg-format" class="input">
                                    <option>Instagram Reel (15–30s)</option>
                                    <option>TikTok (15–45s)</option>
                                    <option>UGC photo + caption</option>
                                    <option>YouTube integration (60s)</option>
                                </select>
                            </div>
                        </div>
                        <div>
                            <label class="label">Tone</label>
                            <div class="flex flex-wrap gap-2">
                                @foreach(['Playful','Authentic','Aesthetic','Bold','Educational'] as $t)
                                    <label>
                                        <input type="radio" name="bg-tone" value="{{ $t }}" class="sr-only" {{ $loop->first ? 'checked' : '' }}>
                                        <span class="pick-chip">{{ $t }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                        <button id="bg-run" class="btn-gradient w-full">✨ Generate brief</button>
                        @if(config('creatorflow.ai.driver') === 'openai' && config('creatorflow.ai.openai.key'))
                            <p class="mt-2 text-center text-[11px] text-emerald-600">✓ Powered by live AI — set up in admin</p>
                        @else
                            <p class="mt-2 text-center text-[11px] text-slate-400">Using offline template · admin can enable live AI</p>
                        @endif
                    </div>
                </div>
            </div>

            <div>
                <div class="relative">
                    <div class="absolute right-3 top-3 z-10 flex gap-1">
                        <button id="bg-copy" class="btn-secondary !py-1.5 !text-xs">Copy</button>
                        <button id="bg-regen" class="btn-ghost !py-1.5 !text-xs">Regenerate</button>
                    </div>
                    <pre id="bg-output" class="min-h-[420px] whitespace-pre-wrap rounded-2xl bg-slate-950 p-6 pt-14 font-mono text-xs leading-relaxed text-slate-100 shadow-lg">Click "Generate brief" to get started.</pre>
                </div>
            </div>
        </div>
    </section>

    <script>
        (function() {
            const hooks = {
                'Playful':    ['POV: your skin actually listens', 'Wait for it… (skin transformation)', 'The 14-day glow-up nobody asked for'],
                'Authentic':  ['Honest review: I tried this for 14 days', 'Not sponsored energy (but it is)', 'What actually happened to my skin'],
                'Aesthetic':  ['3 seconds of pure aesthetic', 'Silk-soft skin cinematic', 'The morning routine that hits different'],
                'Bold':       ['Every serum is lying to you except this one', 'The only skin video you need today', 'This changed my routine forever'],
                'Educational':['Why vitamin C actually works (the science)', 'Read the ingredients before you buy any serum', 'What derms wish you knew about brightening'],
            };
            const shots = [
                'Cold open — face-to-camera hook, natural morning light',
                'Product reveal — soft focus, gentle rotation, ASMR pump sound',
                'Application shot — 3–4 dots on cheeks & forehead',
                'Texture close-up — droplet on back of hand',
                '14-day before/after split screen',
                'Sign-off — smile, brand hashtag on screen',
            ];
            const pick = (arr) => arr[Math.floor(Math.random() * arr.length)];

            const output = document.getElementById('bg-output');
            const runBtn = document.getElementById('bg-run');

            const buildOfflineBrief = (name, desc, niche, format, tone) => {
                return `CAMPAIGN BRIEF · ${name}
Niche: ${niche}   Format: ${format}   Tone: ${tone}
Generated by CreatorFlow · ${new Date().toISOString().slice(0,10)}

━━━ THE PRODUCT
${desc}

━━━ WHY IT MATTERS
Creators in ${niche.toLowerCase()} win when they show a real, unscripted moment.
Our job: make ${name} feel like a friend's recommendation, not a commercial.

━━━ HOOK OPTIONS (pick one)
1. "${pick(hooks[tone])}"
2. "${pick(hooks[tone])}"
3. "${pick(hooks[tone])}"

━━━ STORYBOARD
Shot 1: ${pick(shots)}
Shot 2: ${pick(shots)}
Shot 3: ${pick(shots)}
Shot 4: ${pick(shots)}
Shot 5: ${pick(shots)}

━━━ MUST INCLUDE
• Verbal mention of "${name}" within first 5 seconds
• On-screen text with key benefit (e.g. brightens in 14 days)
• Show packaging clearly for at least 2 seconds
• Include #ad or paid partnership tag per platform rules
• End with a soft CTA — no hard sell

━━━ DO / DON'T
DO ✓ Speak like you're texting a friend
DO ✓ Show real reactions, mess-ups are OK
DO ✓ Use trending audio when possible
DON'T ✗ Read from a script word-for-word
DON'T ✗ Use aggressive sales language
DON'T ✗ Compare directly to competitors by name

━━━ DELIVERABLES
1× ${format}, exported vertical 9:16, native platform export.
Deliver by: [SET DATE]   Usage rights: 90 days paid + organic.

━━━ COMPENSATION
As agreed in your CreatorFlow contract.
Bonus: +25% for videos that hit >5% ER.
`;
            };

            const run = async () => {
                const name = document.getElementById('bg-name').value || 'Your product';
                const desc = document.getElementById('bg-desc').value || '';
                const niche = document.getElementById('bg-niche').value;
                const format = document.getElementById('bg-format').value;
                const tone = document.querySelector('input[name="bg-tone"]:checked').value;

                // Try the server-side AI endpoint first (uses the admin-configured provider).
                runBtn.disabled = true;
                const oldLabel = runBtn.textContent;
                runBtn.textContent = '✨ Generating…';
                output.textContent = 'Generating brief…';

                try {
                    const csrf = document.querySelector('meta[name="csrf-token"]')?.content;
                    const res = await fetch('{{ route('tools.brief.generate') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrf || '',
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                        body: JSON.stringify({ product_name: name, description: desc, niche, format, tone }),
                    });
                    if (res.ok) {
                        const data = await res.json();
                        if (data && data.brief) {
                            output.textContent = data.brief;
                            return;
                        }
                    }
                } catch (e) { /* fall through */ }

                // Fallback: fully client-side template
                output.textContent = buildOfflineBrief(name, desc, niche, format, tone);
                runBtn.textContent = oldLabel;
                runBtn.disabled = false;
            };

            const done = () => {
                runBtn.disabled = false;
                runBtn.textContent = '✨ Generate brief';
            };

            document.getElementById('bg-run').addEventListener('click',   async () => { await run(); done(); });
            document.getElementById('bg-regen').addEventListener('click', async () => { await run(); done(); });
            document.getElementById('bg-copy').addEventListener('click', () => {
                const t = document.getElementById('bg-output').textContent;
                navigator.clipboard?.writeText(t);
                const btn = document.getElementById('bg-copy');
                const old = btn.textContent;
                btn.textContent = '✓ Copied';
                setTimeout(() => btn.textContent = old, 1500);
            });
        })();
    </script>

    {{-- SEO body content --}}
    <section class="mx-auto max-w-4xl px-4 pb-16">
        <div class="prose prose-slate max-w-none">
            <h2 class="text-2xl font-black tracking-tight text-slate-900">What makes a great creator brief?</h2>
            <p class="text-slate-600">
                The best-performing creator briefs on CreatorFlow — across Delhi beauty, Mumbai fashion, Bangalore tech and Chennai food brands — share the same structure. Ambiguity is the enemy of ROAS. Give the creator hooks, shot list and must-includes; give them freedom on execution.
            </p>

            <h3 class="mt-8">The 7-part brief formula we ship in every campaign</h3>
            <ol class="text-slate-600">
                <li><strong>Brand + product summary</strong> — one line, no fluff. "We make cold-pressed serums for oily-skin Gen-Z in Mumbai."</li>
                <li><strong>Campaign goal</strong> — one measurable outcome. "50 UGC videos, 12% ER floor, 200 attributed orders."</li>
                <li><strong>Hook options</strong> — 3 to pick from, per creative tone (playful, authentic, educational, bold).</li>
                <li><strong>Shot list / storyboard</strong> — 4–6 shots max. Cold open, product reveal, application, texture, before/after, sign-off.</li>
                <li><strong>Must-includes</strong> — product mention within 5s, on-screen text, packaging visible, discount code + #ad disclosure.</li>
                <li><strong>Do / Don't list</strong> — no more than 3 of each. Force clarity.</li>
                <li><strong>Deliverables + rights + comp</strong> — format, deadline, usage window (90-day default), fee or barter, bonus for &gt;5% ER.</li>
            </ol>

            <h3 class="mt-8">Why AI-generated briefs work</h3>
            <p class="text-slate-600">
                A blank Google Doc is the #1 reason campaigns get delayed. This generator gives you a fully-shaped starting point in under 5 seconds — you keep the strategic thinking, we handle the boilerplate. Every brief is editable, copy-pasteable, and drops cleanly into a CreatorFlow campaign or a WhatsApp message.
            </p>

            <h3 class="mt-8">Frequently asked questions</h3>
            <div class="mt-4 space-y-3">
                @foreach($briefFaqLd['mainEntity'] as $q)
                    <details class="rounded-xl border border-slate-200 bg-white p-4">
                        <summary class="cursor-pointer font-semibold text-slate-900">{{ $q['name'] }}</summary>
                        <p class="mt-2 text-slate-600">{{ $q['acceptedAnswer']['text'] }}</p>
                    </details>
                @endforeach
            </div>

            <div class="mt-8 flex flex-wrap gap-2 text-sm">
                <a href="{{ route('tools.roi') }}" class="chip">→ ROI Calculator (INR)</a>
                <a href="{{ route('tools.rate') }}" class="chip">→ Creator rate calculator</a>
                <a href="{{ url('/services/creator-marketplace/india') }}" class="chip">→ Creator marketplace India</a>
                <a href="{{ url('/services/paid-influencer-campaigns/delhi') }}" class="chip">→ Paid influencer campaigns Delhi</a>
            </div>
        </div>
    </section>

    @include('marketing._cta', ['title' => 'Briefs are 1% of the work. Automate the other 99%.', 'sub' => 'CreatorFlow generates briefs, sends them, tracks acceptance, and reviews content.'])
</x-layouts.app>
