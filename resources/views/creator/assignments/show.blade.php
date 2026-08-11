<x-layouts.app panel="creator" :title="$assignment->campaign->title">
    <a href="{{ route('creator.assignments.index') }}" class="text-sm text-slate-500 hover:text-slate-800">← My work</a>

    @php
        // Ordered lifecycle for the progress tracker.
        $lifecycle = ['accepted','contract_sent','contract_signed','order_created','shipped','delivered','in_progress','submitted','changes_requested','approved','completed'];
        $currentIdx = array_search($assignment->status, $lifecycle, true);
        if ($currentIdx === false) $currentIdx = -1;

        // The 4 milestones we show, mapped to the earliest lifecycle status that satisfies each.
        $milestones = [
            ['label' => 'Agreement', 'icon' => '📝', 'at' => 'contract_signed'],
            ['label' => 'Ordered',   'icon' => '📦', 'at' => 'order_created'],
            ['label' => 'Delivered', 'icon' => '🚚', 'at' => 'delivered'],
            ['label' => 'Approved',  'icon' => '✅', 'at' => 'approved'],
        ];
    @endphp

    <div class="mt-3 card p-5 md:p-6">
        <div class="flex flex-wrap items-start justify-between gap-3">
            <div>
                <h1 class="text-2xl font-black tracking-tight text-slate-900">{{ $assignment->campaign->title }}</h1>
                <p class="mt-1 text-sm text-slate-500">{{ $assignment->campaign->workspace->name }}</p>
            </div>
            <x-badge :tone="in_array($assignment->status,['approved','completed']) ? 'green' : (in_array($assignment->status,['submitted','changes_requested']) ? 'amber' : 'slate')">
                {{ str_replace('_',' ', $assignment->status) }}
            </x-badge>
        </div>

        {{-- Progress tracker (fixed: only fills as milestones are reached) --}}
        <ol class="mt-6 grid gap-3 sm:grid-cols-4">
            @foreach($milestones as $i => $m)
                @php
                    $mIdx = array_search($m['at'], $lifecycle, true);
                    $done = $currentIdx >= 0 && $mIdx !== false && $currentIdx >= $mIdx;
                    $active = ! $done && (
                        ($i === 0 && $currentIdx < $mIdx) ||
                        ($i > 0 && $currentIdx >= array_search($milestones[$i-1]['at'], $lifecycle, true) && $currentIdx < $mIdx)
                    );
                @endphp
                <li class="relative rounded-2xl border p-3 text-center
                    {{ $done ? 'border-emerald-200 bg-emerald-50/50' : ($active ? 'border-violet-300 bg-violet-50/50' : 'border-slate-200 bg-white') }}">
                    <div class="mx-auto mb-2 grid h-9 w-9 place-items-center rounded-full text-sm font-bold shadow-sm
                        {{ $done ? 'bg-emerald-500 text-white' : ($active ? 'bg-gradient-to-br from-violet-500 to-pink-500 text-white' : 'bg-slate-100 text-slate-400') }}">
                        {{ $done ? '✓' : ($active ? '•' : $i+1) }}
                    </div>
                    <div class="text-sm font-semibold {{ $done ? 'text-emerald-700' : ($active ? 'text-violet-700' : 'text-slate-500') }}">{{ $m['label'] }}</div>
                </li>
            @endforeach
        </ol>
    </div>

    <div class="mt-6 grid gap-5 lg:grid-cols-3">
        <div class="space-y-5 lg:col-span-2">
            <div class="card p-5 md:p-6">
                <h2 class="text-lg font-bold text-slate-900">The product</h2>
                @if($assignment->campaignProduct)
                    <p class="mt-2 text-sm font-medium text-slate-800">{{ $assignment->campaignProduct->product->title }}</p>
                    <p class="mt-1 text-xs text-slate-500">Your code: <code class="rounded bg-slate-100 px-1.5 py-0.5 font-mono">{{ $assignment->discount_code }}</code></p>
                @endif
                @if($assignment->order)
                    <div class="mt-3 grid gap-2 rounded-xl bg-slate-50 p-3 text-sm sm:grid-cols-2">
                        <div><span class="text-slate-500">Order:</span> <span class="font-medium">{{ $assignment->order->order_number }}</span></div>
                        <div><span class="text-slate-500">Tracking:</span> <span class="font-medium">{{ $assignment->order->tracking_number ?: 'Awaiting shipment' }}</span></div>
                    </div>
                @endif
            </div>

            @if($assignment->contract && ! $assignment->contract->signed_by_creator_at)
                <div class="card p-5 md:p-6">
                    <h2 class="text-lg font-bold text-slate-900">Agreement</h2>
                    <p class="mt-2 text-sm text-slate-600">Please review and sign the agreement to continue.</p>
                    <details class="mt-3">
                        <summary class="cursor-pointer text-sm font-semibold text-violet-700 hover:text-violet-900">Read full agreement</summary>
                        <div class="mt-2 max-h-64 overflow-auto whitespace-pre-wrap rounded-xl bg-slate-50 p-3 text-xs">{{ $assignment->contract->body }}</div>
                    </details>
                    <form method="POST" action="{{ route('creator.assignments.contract', $assignment) }}" class="mt-4">
                        @csrf
                        <button class="btn-primary">I agree &amp; sign</button>
                    </form>
                </div>
            @endif

            {{-- SUBMIT CONTENT -- rebuilt with a proper drag & drop upload zone --}}
            <div class="card p-5 md:p-6">
                <h2 class="text-lg font-bold text-slate-900">Submit content</h2>
                <p class="mt-1 text-sm text-slate-500">Upload a photo or short video. AI pre-checks it against the brief before the brand sees it.</p>

                <form method="POST" action="{{ route('creator.assignments.content', $assignment) }}"
                      enctype="multipart/form-data" class="mt-5 space-y-4">
                    @csrf

                    {{-- Type picker as gradient pills --}}
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

                    {{-- BIG DROP ZONE (image OR video) --}}
                    <div>
                        <label class="label">Your file</label>
                        <div id="creator-drop"
                             class="relative cursor-pointer overflow-hidden rounded-2xl border-2 border-dashed border-violet-300 p-8 text-center transition hover:border-violet-500 md:p-10"
                             style="background-image: linear-gradient(135deg, #faf5ff 0%, #fdf2f8 60%, #fff7ed 100%);">
                            <input id="creator-file" type="file" name="file"
                                   accept="image/*,video/*" capture="environment"
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
                            nameEl.textContent = f.name;
                            sizeEl.textContent = fmt(f.size);
                            preview.innerHTML = '';
                            const url = URL.createObjectURL(f);
                            const tag = f.type.startsWith('video/') ? 'video' : 'img';
                            const el = document.createElement(tag);
                            el.src = url;
                            el.className = 'mx-auto max-h-56 rounded-xl object-contain shadow-md';
                            if (tag === 'video') el.controls = true;
                            preview.appendChild(el);
                        };
                        input.addEventListener('change', render);
                        clearBtn?.addEventListener('click', () => { input.value = ''; render(); });

                        ['dragover','dragenter'].forEach(evt => drop.addEventListener(evt, e => {
                            e.preventDefault(); drop.classList.add('border-violet-500','bg-violet-50');
                        }));
                        ['dragleave','drop'].forEach(evt => drop.addEventListener(evt, e => {
                            e.preventDefault(); drop.classList.remove('border-violet-500','bg-violet-50');
                        }));
                        drop.addEventListener('drop', (e) => {
                            if (e.dataTransfer?.files?.length) { input.files = e.dataTransfer.files; render(); }
                        });
                    })();
                </script>
            </div>

            @if($assignment->submissions->isNotEmpty())
                <div class="card p-5 md:p-6">
                    <h2 class="text-lg font-bold text-slate-900">Your submissions</h2>
                    <div class="mt-4 grid gap-3 sm:grid-cols-2">
                        @foreach($assignment->submissions as $sub)
                            <div class="rounded-xl border border-slate-200 p-3">
                                <div class="flex items-center justify-between">
                                    <span class="text-[11px] font-bold uppercase tracking-widest text-slate-500">{{ $sub->type }}</span>
                                    <x-badge :tone="$sub->status === 'approved' ? 'green' : ($sub->status === 'changes_requested' ? 'amber' : 'slate')">{{ str_replace('_',' ', $sub->status) }}</x-badge>
                                </div>
                                @if($sub->path && ! str_starts_with($sub->path, 'external/'))
                                    @if(str_starts_with($sub->type, 'image'))
                                        <img src="{{ Storage::url($sub->path) }}" class="mt-3 aspect-square w-full rounded-lg object-cover">
                                    @else
                                        <video src="{{ Storage::url($sub->path) }}" controls class="mt-3 w-full rounded-lg"></video>
                                    @endif
                                @else
                                    <a href="{{ $sub->external_post_url }}" target="_blank" class="mt-3 block truncate text-sm text-violet-600 hover:underline">{{ $sub->external_post_url }}</a>
                                @endif
                                @if($sub->ai_score)<p class="mt-2 text-xs text-slate-500">AI score: {{ round($sub->ai_score) }}/10</p>@endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <div class="space-y-5">
            <div class="card p-5 md:p-6">
                <h2 class="text-lg font-bold text-slate-900">Brief</h2>
                <div class="prose prose-sm mt-3 max-w-none whitespace-pre-wrap text-slate-700">{{ $assignment->campaign->brief }}</div>
            </div>
            @if($assignment->payout)
                <div class="card p-5 md:p-6">
                    <h2 class="text-lg font-bold text-slate-900">Payout</h2>
                    <p class="mt-2 text-3xl font-black text-slate-900">${{ number_format($assignment->payout->net_cents/100,2) }}</p>
                    <p class="mt-1 text-xs text-slate-500">{{ $assignment->payout->status }} · released after approval</p>
                </div>
            @endif
            <form method="POST" action="{{ route('messages.start', ['campaign' => $assignment->campaign_id, 'creatorId' => $assignment->creator_id]) }}">
                @csrf
                <button type="submit" class="btn-secondary w-full">💬 Message brand</button>
            </form>
        </div>
    </div>
</x-layouts.app>
