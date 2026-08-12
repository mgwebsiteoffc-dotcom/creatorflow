@php
    $user = auth()->user();
    $isCreator = $user && $user->creator && (int) $user->creator->id === (int) $contract->creator_id;
    $isBrand   = $user && $user->workspaces->contains('id', $contract->workspace_id);
    $canSignCreator = $isCreator && ! $contract->signed_by_creator_at && \App\Models\PlatformSetting::feature('contract_esign');
    $canSignBrand   = $isBrand && $contract->signed_by_creator_at && ! $contract->signed_by_brand_at && \App\Models\PlatformSetting::feature('contract_esign');
    $panel = $isCreator ? 'creator' : ($isBrand ? 'brand' : 'guest');
@endphp
<x-layouts.app :panel="$panel" :title="'Contract — '.$contract->title">
    <a href="{{ $isCreator ? route('creator.assignments.index') : ($isBrand ? route('brand.assignments.index') : url('/')) }}" class="text-sm text-slate-500 hover:text-slate-900">← Back</a>

    <div class="mt-2 flex flex-wrap items-start justify-between gap-4">
        <div>
            <h1 class="text-3xl font-black tracking-tight text-slate-900">{{ $contract->title }}</h1>
            <p class="mt-1 text-sm text-slate-500">
                <x-badge :tone="$contract->status === 'signed' ? 'green' : ($contract->status === 'declined' ? 'rose' : 'amber')">{{ $contract->status }}</x-badge>
                @if($contract->expires_at) · expires {{ $contract->expires_at->diffForHumans() }} @endif
            </p>
        </div>
        @if($contract->fee_cents > 0)
            <div class="rounded-2xl bg-gradient-to-br from-emerald-500 to-teal-500 px-5 py-3 text-white shadow-lg">
                <p class="text-[10px] font-bold uppercase tracking-widest opacity-90">Fee</p>
                <p class="text-2xl font-black">₹{{ number_format($contract->fee_cents / 100, 2, '.', ',') }}</p>
            </div>
        @endif
    </div>

    <div class="mt-6 grid gap-6 lg:grid-cols-3">
        <article class="lg:col-span-2 rounded-2xl border border-slate-200 bg-white p-6 md:p-8">
            <div class="prose prose-slate max-w-none whitespace-pre-line">{!! nl2br(e($contract->body)) !!}</div>

            {{-- Signature blocks --}}
            <div class="mt-10 grid gap-6 md:grid-cols-2">
                <div class="rounded-xl border border-slate-200 bg-slate-50 p-5">
                    <p class="text-xs font-bold uppercase tracking-widest text-slate-500">Creator</p>
                    @if($contract->signed_by_creator_at)
                        <p class="mt-2 font-serif text-2xl italic text-slate-900">{{ $contract->creator_signature_name ?: 'signed' }}</p>
                        @if($contract->creator_signature_svg)
                            <div class="mt-2 rounded border border-slate-200 bg-white p-2">{!! $contract->creator_signature_svg !!}</div>
                        @endif
                        <p class="mt-2 text-[11px] text-slate-500">Signed {{ $contract->signed_by_creator_at->format('M j, Y H:i') }} · IP {{ $contract->signature_ip }}</p>
                    @else
                        <p class="mt-2 text-sm text-slate-500">Awaiting creator signature.</p>
                    @endif
                </div>
                <div class="rounded-xl border border-slate-200 bg-slate-50 p-5">
                    <p class="text-xs font-bold uppercase tracking-widest text-slate-500">Brand</p>
                    @if($contract->signed_by_brand_at)
                        <p class="mt-2 font-serif text-2xl italic text-slate-900">{{ $contract->brand_signature_name ?: 'signed' }}</p>
                        @if($contract->brand_signature_svg)
                            <div class="mt-2 rounded border border-slate-200 bg-white p-2">{!! $contract->brand_signature_svg !!}</div>
                        @endif
                        <p class="mt-2 text-[11px] text-slate-500">Countersigned {{ $contract->signed_by_brand_at->format('M j, Y H:i') }} · IP {{ $contract->brand_signature_ip }}</p>
                    @else
                        <p class="mt-2 text-sm text-slate-500">Awaiting brand countersignature.</p>
                    @endif
                </div>
            </div>
        </article>

        <aside class="space-y-6">
            @if($canSignCreator || $canSignBrand)
                <section class="rounded-2xl border border-violet-200 bg-violet-50/40 p-6">
                    <h2 class="text-lg font-black text-slate-900">{{ $canSignCreator ? '✍️ Sign as creator' : '✍️ Countersign as brand' }}</h2>
                    <form method="POST" action="{{ $canSignCreator ? route('contracts.sign', $contract) : route('contracts.countersign', $contract) }}" class="mt-4 space-y-3" data-esign>
                        @csrf
                        <div>
                            <label class="label">Full name</label>
                            <input class="input" name="signature_name" required placeholder="Type your full legal name">
                        </div>
                        <div>
                            <label class="label">Draw signature (optional)</label>
                            <canvas data-sig-canvas class="h-40 w-full cursor-crosshair rounded-xl border border-slate-300 bg-white"></canvas>
                            <input type="hidden" name="signature_svg" data-sig-svg>
                            <button type="button" data-sig-clear class="mt-1 text-xs text-violet-700 hover:underline">Clear</button>
                        </div>
                        @if($canSignCreator)
                            <label class="flex items-start gap-2 text-xs text-slate-600">
                                <input type="checkbox" name="agreed_text" value="1" required class="mt-0.5">
                                <span>I have read and agree to the terms of this contract. My typed name + timestamp + IP form my legally binding electronic signature.</span>
                            </label>
                        @endif
                        <button class="btn-gradient w-full">{{ $canSignCreator ? 'Sign contract' : 'Countersign' }}</button>
                    </form>
                </section>
            @endif

            @if(! \App\Models\PlatformSetting::feature('contract_esign'))
                <section class="rounded-2xl border border-amber-200 bg-amber-50 p-5 text-xs text-amber-900">
                    <p class="font-bold">📴 E-signature is currently disabled by the admin.</p>
                    <p class="mt-1">Admins can enable it in <em>Integrations → Feature flags → Contract e-signature</em>.</p>
                </section>
            @endif
        </aside>
    </div>

    <script>
    (function() {
        // Simple signature pad on the canvas element — writes an SVG path to a hidden input.
        document.querySelectorAll('[data-sig-canvas]').forEach(canvas => {
            const ctx = canvas.getContext('2d');
            const hidden = canvas.parentElement.querySelector('[data-sig-svg]');
            const clear = canvas.parentElement.querySelector('[data-sig-clear]');
            let drawing = false;
            let paths = [];
            let current = [];
            const rect = () => canvas.getBoundingClientRect();
            const pos = (e) => {
                const r = rect();
                const t = e.touches ? e.touches[0] : e;
                return [((t.clientX - r.left) * canvas.width) / r.width, ((t.clientY - r.top) * canvas.height) / r.height];
            };
            // Set canvas resolution
            const resize = () => { canvas.width = canvas.clientWidth * 2; canvas.height = canvas.clientHeight * 2; ctx.lineWidth = 3; ctx.lineCap = 'round'; ctx.strokeStyle = '#0f172a'; };
            resize();
            new ResizeObserver(resize).observe(canvas);
            const start = (e) => { drawing = true; current = [pos(e)]; e.preventDefault(); };
            const move  = (e) => { if (!drawing) return; const p = pos(e); const [px, py] = current[current.length - 1]; ctx.beginPath(); ctx.moveTo(px, py); ctx.lineTo(p[0], p[1]); ctx.stroke(); current.push(p); e.preventDefault(); };
            const end   = () => { if (!drawing) return; drawing = false; paths.push(current); syncSvg(); };
            const syncSvg = () => {
                let d = '';
                for (const path of paths) {
                    if (!path.length) continue;
                    d += 'M' + path.map(p => p[0].toFixed(1)+','+p[1].toFixed(1)).join(' L');
                }
                hidden.value = d ? `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 ${canvas.width} ${canvas.height}"><path d="${d}" stroke="#0f172a" stroke-width="3" fill="none" stroke-linecap="round"/></svg>` : '';
            };
            canvas.addEventListener('mousedown', start); canvas.addEventListener('mousemove', move); window.addEventListener('mouseup', end);
            canvas.addEventListener('touchstart', start); canvas.addEventListener('touchmove', move); canvas.addEventListener('touchend', end);
            clear?.addEventListener('click', () => { paths = []; ctx.clearRect(0, 0, canvas.width, canvas.height); hidden.value = ''; });
        });
    })();
    </script>
</x-layouts.app>
