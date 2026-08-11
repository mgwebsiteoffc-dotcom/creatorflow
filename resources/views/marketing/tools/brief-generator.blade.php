<x-layouts.app panel="guest" title="AI brief generator">
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

            const run = () => {
                const name = document.getElementById('bg-name').value || 'Your product';
                const desc = document.getElementById('bg-desc').value || '';
                const niche = document.getElementById('bg-niche').value;
                const format = document.getElementById('bg-format').value;
                const tone = document.querySelector('input[name="bg-tone"]:checked').value;

                const brief = `CAMPAIGN BRIEF · ${name}
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
                document.getElementById('bg-output').textContent = brief;
            };

            document.getElementById('bg-run').addEventListener('click', run);
            document.getElementById('bg-regen').addEventListener('click', run);
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

    @include('marketing._cta', ['title' => 'Briefs are 1% of the work. Automate the other 99%.', 'sub' => 'CreatorFlow generates briefs, sends them, tracks acceptance, and reviews content.'])
</x-layouts.app>
