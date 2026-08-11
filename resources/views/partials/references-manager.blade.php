@props(['campaign'])
@php $refs = $campaign->references ?? collect(); @endphp

<section class="rounded-2xl border border-slate-200 bg-white p-5 md:p-6">
    <div class="flex items-start justify-between gap-3">
        <div class="flex items-center gap-2">
            <div class="grid h-9 w-9 place-items-center rounded-xl bg-gradient-to-br from-cyan-500 to-emerald-500 text-white">📎</div>
            <div>
                <h2 class="text-lg font-bold text-slate-900">Reference material</h2>
                <p class="text-xs text-slate-500">Attach images, videos, PDFs, or links. Creators see everything here.</p>
            </div>
        </div>
        <span class="text-xs font-semibold text-slate-500">{{ $refs->count() }} shared</span>
    </div>

    {{-- Tabs for two add modes --}}
    <div class="mt-5" data-tabs>
        <div class="flex w-fit gap-1 rounded-full border border-slate-200 bg-slate-50 p-1">
            <button type="button" data-tab="upload" class="tab-pill">📤 Upload file</button>
            <button type="button" data-tab="link"   class="tab-pill">🔗 Paste link</button>
        </div>

        {{-- File upload panel --}}
        <div data-panel="upload" class="mt-4">
            <form method="POST" action="{{ route('brand.campaigns.references.store', $campaign) }}"
                  enctype="multipart/form-data" class="space-y-3">
                @csrf
                <input type="hidden" name="kind" value="file">
                <div id="ref-drop"
                     class="relative cursor-pointer overflow-hidden rounded-2xl border-2 border-dashed border-cyan-300 p-6 text-center transition hover:border-cyan-500"
                     style="background-image: linear-gradient(135deg,#ecfeff 0%,#f0fdfa 60%,#fefce8 100%);">
                    <input id="ref-input" type="file" name="files[]" multiple
                           accept=".jpg,.jpeg,.png,.webp,.gif,.mp4,.mov,.pdf,.zip,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt,.csv"
                           class="absolute inset-0 h-full w-full cursor-pointer opacity-0">
                    <div class="pointer-events-none">
                        <div class="mx-auto grid h-12 w-12 place-items-center rounded-2xl text-xl text-white shadow"
                             style="background-image: linear-gradient(135deg,#06b6d4,#10b981);">📎</div>
                        <div class="mt-3 text-sm font-bold text-slate-900">Drop files here or click to browse</div>
                        <div class="mt-0.5 text-xs text-slate-500">Images · videos · PDFs · docs — up to 50 MB each</div>
                    </div>
                </div>
                <p id="ref-files-summary" class="hidden text-xs font-semibold text-slate-600"></p>
                <div class="grid gap-3 sm:grid-cols-2">
                    <input class="input" name="title" placeholder="Title (optional)">
                    <input class="input" name="note" placeholder="Note for creators (optional)">
                </div>
                <button class="btn-primary w-full !py-2 sm:w-auto">Add reference(s)</button>
            </form>
            <script>
                (function(){
                    const inp = document.getElementById('ref-input');
                    const drop = document.getElementById('ref-drop');
                    const summary = document.getElementById('ref-files-summary');
                    if (!inp) return;
                    const render = () => {
                        const files = inp.files ? Array.from(inp.files) : [];
                        if (!files.length) { summary.classList.add('hidden'); return; }
                        summary.classList.remove('hidden');
                        summary.textContent = files.length === 1
                            ? `Selected: ${files[0].name}`
                            : `${files.length} files selected: ${files.slice(0,3).map(f=>f.name).join(', ')}${files.length>3?'…':''}`;
                    };
                    inp.addEventListener('change', render);
                    ['dragover','dragenter'].forEach(evt => drop.addEventListener(evt, e => { e.preventDefault(); drop.classList.add('border-cyan-500','bg-cyan-50'); }));
                    ['dragleave','drop'].forEach(evt => drop.addEventListener(evt, e => { e.preventDefault(); drop.classList.remove('border-cyan-500','bg-cyan-50'); }));
                    drop.addEventListener('drop', e => { if (e.dataTransfer?.files?.length) { inp.files = e.dataTransfer.files; render(); } });
                })();
            </script>
        </div>

        {{-- Link panel --}}
        <div data-panel="link" class="mt-4 hidden">
            <form method="POST" action="{{ route('brand.campaigns.references.store', $campaign) }}" class="space-y-3">
                @csrf
                <input type="hidden" name="kind" value="link">
                <div class="grid gap-3 sm:grid-cols-2">
                    <input class="input" name="url" type="url" required placeholder="https://drive.google.com/... or reference URL">
                    <input class="input" name="title" placeholder="Title (optional)">
                </div>
                <input class="input" name="note" placeholder="Why this link matters (optional)">
                <button class="btn-primary w-full !py-2 sm:w-auto">Add link</button>
            </form>
        </div>
    </div>

    {{-- Existing references --}}
    <div class="mt-6 space-y-2">
        @forelse($refs as $ref)
            @include('partials.reference-tile', ['ref' => $ref, 'canDelete' => true, 'campaign' => $campaign])
        @empty
            <p class="rounded-xl border border-dashed border-slate-200 bg-slate-50 p-4 text-center text-xs text-slate-500">
                No references yet — add mood boards, sample content, or link to a Google Drive folder.
            </p>
        @endforelse
    </div>
</section>
