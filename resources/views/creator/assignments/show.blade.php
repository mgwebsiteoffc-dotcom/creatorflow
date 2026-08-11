<x-layouts.app panel="creator" :title="$assignment->campaign->title">
    <a href="{{ route('creator.assignments.index') }}" class="text-sm text-slate-500 hover:text-slate-800">← My work</a>

    @php
        $lifecycle = ['accepted','contract_sent','contract_signed','order_created','shipped','delivered','in_progress','submitted','changes_requested','approved','completed'];
        $currentIdx = array_search($assignment->status, $lifecycle, true);
        if ($currentIdx === false) $currentIdx = -1;

        $milestones = [
            ['label' => 'Agreement', 'icon' => '📝', 'at' => 'contract_signed'],
            ['label' => 'Ordered',   'icon' => '📦', 'at' => 'order_created'],
            ['label' => 'Delivered', 'icon' => '🚚', 'at' => 'delivered'],
            ['label' => 'Approved',  'icon' => '✅', 'at' => 'approved'],
        ];
        $activeIdx = 0;
        foreach ($milestones as $i => $m) {
            $mIdx = array_search($m['at'], $lifecycle, true);
            if ($currentIdx >= $mIdx) { $activeIdx = $i + 1; }
        }

        $refs = $assignment->campaign->references ?? collect();
    @endphp

    {{-- HERO --}}
    <div class="mt-3 overflow-hidden rounded-3xl border border-slate-200 bg-white">
        <div class="relative h-24 md:h-28"
             style="background-image: linear-gradient(120deg,#7c3aed 0%,#ec4899 50%,#f59e0b 110%);">
            <div class="absolute inset-0 opacity-30"
                 style="background: radial-gradient(400px 200px at 20% 30%, rgba(255,255,255,.55), transparent 60%);"></div>
        </div>
        <div class="p-5 md:p-7">
            <div class="flex flex-wrap items-start justify-between gap-3">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-widest text-violet-700">{{ $assignment->campaign->workspace->name ?? 'Brand' }}</p>
                    <h1 class="mt-1 text-2xl font-black tracking-tight text-slate-900 md:text-3xl">{{ $assignment->campaign->title }}</h1>
                    @if($assignment->content_due_date)
                        <p class="mt-2 text-sm text-slate-500">📅 Content due <span class="font-semibold text-slate-800">{{ $assignment->content_due_date->format('M j, Y') }}</span></p>
                    @endif
                </div>
                <x-badge :tone="in_array($assignment->status,['approved','completed']) ? 'green' : (in_array($assignment->status,['submitted','changes_requested']) ? 'amber' : 'violet')">
                    {{ str_replace('_',' ', $assignment->status) }}
                </x-badge>
            </div>

            {{-- Progress tracker (fixed) --}}
            <ol class="mt-6 grid gap-3 sm:grid-cols-4">
                @foreach($milestones as $i => $m)
                    @php
                        $done = $i < $activeIdx;
                        $isNow = $i === $activeIdx && $activeIdx < count($milestones);
                    @endphp
                    <li class="relative rounded-2xl border p-3 text-center transition
                        {{ $done ? 'border-emerald-200 bg-emerald-50/60' :
                          ($isNow ? 'border-violet-300 bg-violet-50/60 shadow-[0_10px_25px_-15px_rgba(124,58,237,.5)]' :
                                    'border-slate-200 bg-white') }}">
                        <div class="mx-auto mb-2 grid h-9 w-9 place-items-center rounded-full text-sm font-bold shadow-sm
                            {{ $done ? 'bg-emerald-500 text-white' :
                              ($isNow ? 'bg-gradient-to-br from-violet-500 to-pink-500 text-white animate-pulse' :
                                        'bg-slate-100 text-slate-400') }}">
                            {{ $done ? '✓' : ($isNow ? '•' : $i+1) }}
                        </div>
                        <div class="text-sm font-semibold {{ $done ? 'text-emerald-700' : ($isNow ? 'text-violet-700' : 'text-slate-500') }}">{{ $m['label'] }}</div>
                    </li>
                @endforeach
            </ol>
        </div>
    </div>

    {{-- 2-column layout: main tasks on left, meta on right --}}
    <div class="mt-6 grid gap-6 lg:grid-cols-3">
        <div class="space-y-6 lg:col-span-2">

            {{-- PRODUCT + ORDER --}}
            <div class="rounded-2xl border border-slate-200 bg-white p-5 md:p-6">
                <div class="flex items-start gap-4">
                    <div class="grid h-14 w-14 shrink-0 place-items-center rounded-2xl bg-gradient-to-br from-violet-100 to-pink-100 text-2xl">
                        📦
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-xs font-bold uppercase tracking-widest text-slate-500">Your product</p>
                        <p class="mt-0.5 truncate text-lg font-bold text-slate-900">{{ $assignment->campaignProduct?->product?->title ?? '—' }}</p>
                        @if($assignment->discount_code)
                            <div class="mt-3 flex flex-wrap items-center gap-2">
                                <span class="text-xs text-slate-500">Your discount code</span>
                                <button type="button" data-copy="{{ $assignment->discount_code }}"
                                        class="inline-flex items-center gap-2 rounded-lg border border-violet-200 bg-violet-50 px-3 py-1 font-mono text-sm font-bold text-violet-700 transition hover:bg-violet-100">
                                    <span>{{ $assignment->discount_code }}</span>
                                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 5H6a2 2 0 00-2 2v11a2 2 0 002 2h9a2 2 0 002-2v-1M9 3h9a2 2 0 012 2v9a2 2 0 01-2 2h-9a2 2 0 01-2-2V5a2 2 0 012-2z"/></svg>
                                </button>
                                <span data-copy-msg class="hidden text-xs font-semibold text-emerald-600">Copied ✓</span>
                            </div>
                        @endif
                    </div>
                </div>
                @if($assignment->order)
                    <div class="mt-4 grid gap-2 rounded-xl bg-slate-50 p-3 text-sm sm:grid-cols-2">
                        <div><span class="text-slate-500">Order</span> <span class="font-semibold text-slate-800">#{{ $assignment->order->order_number }}</span></div>
                        <div><span class="text-slate-500">Tracking</span> <span class="font-semibold text-slate-800">{{ $assignment->order->tracking_number ?: 'Awaiting shipment' }}</span></div>
                    </div>
                @endif
            </div>

            {{-- AGREEMENT --}}
            @if($assignment->contract && ! $assignment->contract->signed_by_creator_at)
                <div class="rounded-2xl border border-amber-200 bg-gradient-to-br from-amber-50 to-orange-50 p-5 md:p-6">
                    <div class="flex items-start gap-3">
                        <div class="grid h-11 w-11 shrink-0 place-items-center rounded-xl bg-white text-xl shadow-sm">📝</div>
                        <div class="min-w-0 flex-1">
                            <h2 class="text-lg font-bold text-slate-900">Sign the agreement</h2>
                            <p class="mt-1 text-sm text-slate-600">Standard contract — usage rights, deliverables and payment.</p>
                            <details class="mt-3">
                                <summary class="cursor-pointer text-sm font-semibold text-violet-700 hover:text-violet-900">Read full agreement</summary>
                                <div class="mt-2 max-h-64 overflow-auto whitespace-pre-wrap rounded-xl bg-white p-3 text-xs text-slate-700">{{ $assignment->contract->body }}</div>
                            </details>
                            <form method="POST" action="{{ route('creator.assignments.contract', $assignment) }}" class="mt-4">
                                @csrf
                                <button class="btn-primary">✓ I agree &amp; sign</button>
                            </form>
                        </div>
                    </div>
                </div>
            @endif

            {{-- SUBMIT CONTENT --}}
            <div class="rounded-2xl border border-slate-200 bg-white p-5 md:p-6">
                <div class="flex items-start gap-3">
                    <div class="grid h-11 w-11 shrink-0 place-items-center rounded-xl bg-gradient-to-br from-violet-500 to-pink-500 text-xl text-white shadow-sm">🎬</div>
                    <div>
                        <h2 class="text-lg font-bold text-slate-900">Submit your content</h2>
                        <p class="mt-1 text-sm text-slate-500">Upload a photo or short video. AI pre-checks it against the brief before the brand sees it.</p>
                    </div>
                </div>

                <form method="POST" action="{{ route('creator.assignments.content', $assignment) }}"
                      enctype="multipart/form-data" class="mt-5 space-y-4">
                    @csrf

                    {{-- Type picker --}}
                    <div>
                        <label class="label">Content type</label>
                        <div class="flex flex-wrap gap-2">
                            @foreach([
                                ['video','🎬 Video'],
                                ['image','📷 Image'],
                                ['reel','🎞 Reel'],
                                ['story','⭐ Story'],
                                ['link','🔗 Live link'],
                            ] as $opt)
                                <label>
                                    <input type="radio" name="type" value="{{ $opt[0] }}" class="sr-only" {{ $loop->first ? 'checked' : '' }}>
                                    <span class="pick-chip">{{ $opt[1] }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    {{-- DROP ZONE --}}
                    <div>
                        <label class="label">Your file</label>
                        <div id="creator-drop"
                             class="relative cursor-pointer overflow-hidden rounded-2xl border-2 border-dashed border-violet-300 p-8 text-center transition hover:border-violet-500 md:p-10"
                             style="background-image: linear-gradient(135deg, #faf5ff 0%, #fdf2f8 60%, #fff7ed 100%);">
                            <input id="creator-file" type="file" name="file" accept="image/*,video/*" capture="environment"
                                   class="absolute inset-0 h-full w-full cursor-pointer opacity-0">
                            <div id="creator-drop-empty" class="pointer-events-none">
                                <div class="mx-auto grid h-16 w-16 place-items-center rounded-2xl text-2xl text-white shadow-lg"
                                     style="background-image: linear-gradient(135deg,#7c3aed,#ec4899 60%,#f59e0b);">📤</div>
                                <div class="mt-4 text-base font-bold text-slate-900">Tap to upload or drop file here</div>
                                <div class="mt-1 text-xs text-slate-500">JPG · PNG · MP4 · MOV · up to 100 MB</div>
                                <div class="mt-4 inline-flex items-center gap-2 rounded-full border border-violet-200 bg-white px-3 py-1.5 text-xs font-semibold text-violet-700 shadow-sm">
                                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" d="M12 5v14M5 12h14"/></svg>
                                    Choose file
                                </div>
                            </div>
                            <div id="creator-drop-filled" class="pointer-events-none hidden">
                                <div id="creator-preview"></div>
                                <div class="mt-3 text-sm font-semibold text-slate-900 truncate" id="creator-filename"></div>
                                <div class="text-xs text-slate-500" id="creator-filesize"></div>
                                <button type="button" id="creator-clear" class="pointer-events-auto mt-3 inline-flex items-center gap-1 text-xs font-semibold text-rose-600 hover:text-rose-800">
                                    ✕ Remove &amp; pick another
                                </button>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="label">Caption / notes <span class="ml-1 text-xs font-normal text-slate-400">(optional)</span></label>
                        <textarea name="caption" class="input min-h-24" placeholder="Anything the brand should know? Hooks, timestamps, tags…"></textarea>
                    </div>

                    <div>
                        <label class="label">Live post URL <span class="ml-1 text-xs font-normal text-slate-400">(optional — for already-posted content)</span></label>
                        <input type="url" name="external_post_url" class="input" placeholder="https://instagram.com/p/…">
                    </div>

                    <button class="btn-gradient w-full">🚀 Submit for review</button>
                </form>
            </div>

            {{-- YOUR SUBMISSIONS --}}
            @if($assignment->submissions->isNotEmpty())
                <div class="rounded-2xl border border-slate-200 bg-white p-5 md:p-6">
                    <h2 class="text-lg font-bold text-slate-900">Your submissions</h2>
                    <div class="mt-4 grid gap-3 sm:grid-cols-2">
                        @foreach($assignment->submissions as $sub)
                            <div class="overflow-hidden rounded-xl border border-slate-200">
                                @if($sub->path && ! str_starts_with($sub->path, 'external/'))
                                    @if(str_starts_with($sub->type, 'image'))
                                        <img src="{{ Storage::url($sub->path) }}" class="aspect-square w-full object-cover" alt="">
                                    @else
                                        <video src="{{ Storage::url($sub->path) }}" controls class="aspect-video w-full bg-black"></video>
                                    @endif
                                @elseif($sub->external_post_url)
                                    <a href="{{ $sub->external_post_url }}" target="_blank" class="grid aspect-video place-items-center bg-slate-100 text-sm font-semibold text-violet-700 hover:underline">
                                        {{ Str::limit($sub->external_post_url, 40) }}
                                    </a>
                                @endif
                                <div class="p-3">
                                    <div class="flex items-center justify-between">
                                        <span class="text-[11px] font-bold uppercase tracking-widest text-slate-500">{{ $sub->type }}</span>
                                        <x-badge :tone="$sub->status === 'approved' ? 'green' : ($sub->status === 'changes_requested' ? 'amber' : 'slate')">{{ str_replace('_',' ', $sub->status) }}</x-badge>
                                    </div>
                                    @if($sub->ai_score)
                                        <div class="mt-2 flex items-center gap-2 text-xs text-slate-500">
                                            <div class="h-1.5 flex-1 rounded-full bg-slate-100">
                                                <div class="h-1.5 rounded-full" style="width: {{ min(100, $sub->ai_score * 10) }}%; background-image: linear-gradient(90deg,#7c3aed,#ec4899);"></div>
                                            </div>
                                            <span class="shrink-0 font-semibold">AI {{ round($sub->ai_score, 1) }}/10</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        {{-- RIGHT COLUMN --}}
        <aside class="space-y-6">
            {{-- BRIEF (rendered markdown) --}}
            <div class="rounded-2xl border border-slate-200 bg-white p-5 md:p-6">
                <div class="flex items-center gap-2">
                    <div class="grid h-9 w-9 place-items-center rounded-xl bg-gradient-to-br from-violet-500 to-pink-500 text-white">📋</div>
                    <h2 class="text-lg font-bold text-slate-900">Brief</h2>
                </div>
                <div class="mt-4">
                    <x-brief :markdown="$assignment->campaign->brief" />
                </div>
            </div>

            {{-- REFERENCES --}}
            @if($refs->isNotEmpty())
                <div class="rounded-2xl border border-slate-200 bg-white p-5 md:p-6">
                    <div class="flex items-center gap-2">
                        <div class="grid h-9 w-9 place-items-center rounded-xl bg-gradient-to-br from-cyan-500 to-emerald-500 text-white">📎</div>
                        <h2 class="text-lg font-bold text-slate-900">References</h2>
                        <span class="ml-auto text-xs font-semibold text-slate-500">{{ $refs->count() }} shared</span>
                    </div>
                    <p class="mt-2 text-xs text-slate-500">Shared by the brand as inspiration or examples.</p>
                    <div class="mt-4 space-y-2">
                        @foreach($refs as $ref)
                            @include('partials.reference-tile', ['ref' => $ref, 'canDelete' => false])
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- PAYOUT --}}
            @if($assignment->payout)
                <div class="overflow-hidden rounded-2xl bg-gradient-to-br from-emerald-500 to-teal-500 p-5 text-white shadow-lg md:p-6">
                    <div class="text-xs font-bold uppercase tracking-widest opacity-90">Payout</div>
                    <div class="mt-1 text-4xl font-black">${{ number_format($assignment->payout->net_cents/100,2) }}</div>
                    <div class="mt-1 text-xs opacity-90">{{ $assignment->payout->status }} · released after approval</div>
                </div>
            @endif

            <form method="POST" action="{{ route('messages.start', ['campaign' => $assignment->campaign_id, 'creatorId' => $assignment->creator_id]) }}">
                @csrf
                <button type="submit" class="btn-secondary w-full">💬 Message brand</button>
            </form>
        </aside>
    </div>

    <script>
        (function () {
            const input = document.getElementById('creator-file');
            const drop = document.getElementById('creator-drop');
            const empty = document.getElementById('creator-drop-empty');
            const filled = document.getElementById('creator-drop-filled');
            const preview = document.getElementById('creator-preview');
            const nameEl = document.getElementById('creator-filename');
            const sizeEl = document.getElementById('creator-filesize');
            const clearBtn = document.getElementById('creator-clear');
            if (!input) return;

            const fmt = (b) => b < 1024*1024 ? (b/1024).toFixed(1)+' KB' : (b/1024/1024).toFixed(1)+' MB';
            const render = () => {
                const f = input.files && input.files[0];
                if (!f) { empty.classList.remove('hidden'); filled.classList.add('hidden'); preview.innerHTML=''; return; }
                empty.classList.add('hidden'); filled.classList.remove('hidden');
                nameEl.textContent = f.name; sizeEl.textContent = fmt(f.size);
                preview.innerHTML = '';
                const url = URL.createObjectURL(f);
                const tag = f.type.startsWith('video/') ? 'video' : 'img';
                const el = document.createElement(tag);
                el.src = url; el.className = 'mx-auto max-h-56 rounded-xl object-contain shadow-md';
                if (tag === 'video') el.controls = true;
                preview.appendChild(el);
            };
            input.addEventListener('change', render);
            clearBtn?.addEventListener('click', () => { input.value = ''; render(); });
            ['dragover','dragenter'].forEach(evt => drop.addEventListener(evt, e => { e.preventDefault(); drop.classList.add('border-violet-500','bg-violet-50'); }));
            ['dragleave','drop'].forEach(evt => drop.addEventListener(evt, e => { e.preventDefault(); drop.classList.remove('border-violet-500','bg-violet-50'); }));
            drop.addEventListener('drop', (e) => { if (e.dataTransfer?.files?.length) { input.files = e.dataTransfer.files; render(); } });

            // Copy discount code
            document.querySelectorAll('[data-copy]').forEach(btn => {
                btn.addEventListener('click', () => {
                    navigator.clipboard?.writeText(btn.dataset.copy);
                    const msg = btn.parentElement.querySelector('[data-copy-msg]');
                    if (msg) { msg.classList.remove('hidden'); setTimeout(() => msg.classList.add('hidden'), 2000); }
                });
            });
        })();
    </script>
</x-layouts.app>
