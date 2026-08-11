<x-layouts.app panel="guest" title="ROI Calculator">
    <section class="relative overflow-hidden">
        <div class="aurora"></div>
        <div class="mx-auto max-w-6xl px-4 pb-16 pt-14 md:pt-20">
            <nav class="text-xs text-slate-500">
                <a href="{{ route('tools.index') }}" class="hover:text-slate-900">Tools</a> → <span>ROI calculator</span>
            </nav>
            <h1 class="mt-3 text-4xl font-black tracking-tight text-slate-900 sm:text-5xl">
                Creator campaign <span class="text-gradient">ROI calculator</span>
            </h1>
            <p class="mt-3 max-w-2xl text-slate-600">Model expected reach, engagements, and revenue. Adjust the sliders — results update live.</p>
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
                            $inputs = [
                                ['creators', 'Number of creators', 30, 1, 500],
                                ['followers', 'Avg followers per creator', 25000, 1000, 500000, 1000],
                                ['er', 'Engagement rate %', 5, 0.1, 20, 0.1],
                                ['cvr', 'Conversion rate %', 1.5, 0.1, 10, 0.1],
                                ['aov', 'Avg order value $', 60, 5, 500],
                                ['cost', 'Cost per creator $', 120, 0, 5000],
                            ];
                        @endphp
                        <div class="mt-6 space-y-5">
                            @foreach($inputs as [$id, $label, $val, $min, $max, $step])
                                <div>
                                    <div class="flex items-center justify-between">
                                        <label class="text-sm font-medium text-slate-700" for="{{ $id }}">{{ $label }}</label>
                                        <input id="{{ $id }}-num" class="w-24 rounded-lg border border-slate-200 bg-white px-2 py-1 text-right text-sm font-semibold text-slate-900" type="number" value="{{ $val }}" step="{{ $step ?? 1 }}" min="{{ $min }}" max="{{ $max }}">
                                    </div>
                                    <input id="{{ $id }}" type="range" min="{{ $min }}" max="{{ $max }}" step="{{ $step ?? 1 }}" value="{{ $val }}" class="mt-2 h-2 w-full cursor-pointer appearance-none rounded-full bg-slate-100 accent-violet-600">
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
            const fmt = (n) => new Intl.NumberFormat('en-US', { maximumFractionDigits: 0 }).format(n);
            const fmt$ = (n) => '$' + fmt(n);

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

    @include('marketing._cta', ['title' => 'Model it here. Run it inside CreatorFlow.', 'sub' => 'Free plan includes 5 campaigns/month with real-time attribution baked in.'])
</x-layouts.app>
