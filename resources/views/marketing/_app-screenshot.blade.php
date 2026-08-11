@props(['variant' => 0])

<div class="relative">
    <div class="absolute -inset-6 -z-10 rounded-[2rem] bg-gradient-to-tr from-violet-300/40 via-pink-300/30 to-cyan-300/40 blur-2xl"></div>

    {{-- Browser chrome --}}
    <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-[0_30px_80px_-30px_rgba(15,23,42,.25)]">
        <div class="flex items-center gap-2 border-b border-slate-100 bg-slate-50 px-3 py-2">
            <span class="h-2.5 w-2.5 rounded-full bg-rose-400"></span>
            <span class="h-2.5 w-2.5 rounded-full bg-amber-400"></span>
            <span class="h-2.5 w-2.5 rounded-full bg-emerald-400"></span>
            <span class="ml-3 flex-1 truncate rounded-md bg-white px-2 py-0.5 text-[10px] text-slate-500 ring-1 ring-slate-200">app.creatorplex.io/brand</span>
        </div>

        {{-- ─────────── VARIANT 0 · Campaign management ─────────── --}}
        @if($variant === 0)
            <div class="grid grid-cols-[140px_1fr] bg-white">
                {{-- side nav --}}
                <nav class="border-r border-slate-100 bg-slate-50 p-3 text-[11px] space-y-1">
                    <div class="rounded-md bg-white px-2 py-1.5 font-semibold text-slate-900 shadow-sm">🏠 Home</div>
                    <div class="rounded-md px-2 py-1.5 text-violet-700 bg-violet-50 font-semibold">🚀 Campaigns</div>
                    <div class="rounded-md px-2 py-1.5 text-slate-500">📥 Applications</div>
                    <div class="rounded-md px-2 py-1.5 text-slate-500">📦 Products</div>
                    <div class="rounded-md px-2 py-1.5 text-slate-500">🎬 Creators</div>
                    <div class="rounded-md px-2 py-1.5 text-slate-500">📊 Analytics</div>
                </nav>
                <div class="p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="text-xs font-bold uppercase tracking-widest text-violet-700">Campaign</div>
                            <div class="text-sm font-black text-slate-900">Summer glow drop</div>
                        </div>
                        <span class="rounded-full bg-emerald-100 px-2 py-0.5 text-[10px] font-bold text-emerald-700">● LIVE</span>
                    </div>
                    <div class="mt-3 grid grid-cols-4 gap-2">
                        @foreach([['Invited','128'], ['Accepted','42'], ['Content','36'], ['Approved','28']] as $m)
                            <div class="rounded-lg bg-slate-50 p-2 text-center">
                                <div class="text-[9px] uppercase tracking-widest text-slate-400">{{ $m[0] }}</div>
                                <div class="text-sm font-black text-slate-900">{{ $m[1] }}</div>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-4 rounded-xl border border-violet-200 bg-violet-50/60 p-3">
                        <div class="flex items-center gap-2 text-[11px] font-bold text-violet-700">📥 3 new applications</div>
                        <div class="mt-2 space-y-1.5">
                            @foreach([['A','Aria Kim','98% match','#f472b6'], ['T','Theo V.','92% match','#a78bfa'], ['N','Nova E.','88% match','#22d3ee']] as $a)
                                <div class="flex items-center gap-2 rounded-md bg-white px-2 py-1.5 text-[11px] shadow-sm">
                                    <span class="grid h-6 w-6 place-items-center rounded-full text-[10px] font-bold text-white" style="background:{{ $a[3] }}">{{ $a[0] }}</span>
                                    <div class="flex-1 truncate font-semibold text-slate-800">{{ $a[1] }}</div>
                                    <span class="text-emerald-600 font-bold">{{ $a[2] }}</span>
                                    <span class="rounded bg-violet-600 px-1.5 py-0.5 text-white">✓</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

        {{-- ─────────── VARIANT 1 · Creator marketplace ─────────── --}}
        @elseif($variant === 1)
            <div class="p-5">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-xs font-bold uppercase tracking-widest text-violet-700">Marketplace</div>
                        <div class="text-sm font-black text-slate-900">1,283 creators matched</div>
                    </div>
                    <div class="flex gap-1 text-[10px]">
                        <span class="rounded-full bg-violet-600 px-2 py-0.5 font-bold text-white">Beauty</span>
                        <span class="rounded-full bg-slate-100 px-2 py-0.5 text-slate-600">India</span>
                        <span class="rounded-full bg-slate-100 px-2 py-0.5 text-slate-600">20K–150K</span>
                    </div>
                </div>

                <div class="mt-4 grid grid-cols-3 gap-2">
                    @php
                        $cards = [
                            ['A','@aria.k','620K','8.1%','#f472b6'],
                            ['M','@mira.reels','780K','6.4%','#c084fc'],
                            ['N','@nova.eats','340K','12.4%','#f59e0b'],
                            ['T','@theovlog','1.2M','5.4%','#22d3ee'],
                            ['K','@techkai','2.1M','4.8%','#38bdf8'],
                            ['Z','@zia.styles','3.4M','7.2%','#34d399'],
                        ];
                    @endphp
                    @foreach($cards as $c)
                        <div class="overflow-hidden rounded-lg border border-slate-100">
                            <div class="relative h-16" style="background:{{ $c[4] }}">
                                <span class="absolute right-1 top-1 rounded bg-white/40 px-1 text-[9px] font-bold text-white backdrop-blur">✓</span>
                            </div>
                            <div class="p-1.5">
                                <div class="truncate text-[10px] font-bold text-slate-900">{{ $c[1] }}</div>
                                <div class="flex justify-between text-[9px] text-slate-500">
                                    <span>{{ $c[2] }} </span>
                                    <span class="text-emerald-600 font-bold">{{ $c[3] }}</span>
                                </div>
                                <div class="mt-1 h-1 rounded-full bg-slate-100">
                                    <div class="h-1 rounded-full" style="width: {{ [92,85,88,72,78,80][$loop->index] }}%; background-image: linear-gradient(90deg,#7c3aed,#ec4899);"></div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-4 flex items-center justify-between text-[11px]">
                    <span class="text-slate-500">Selected: <strong class="text-slate-800">4 creators</strong></span>
                    <button class="rounded-md px-2.5 py-1 font-bold text-white shadow-sm" style="background-image: linear-gradient(135deg,#7c3aed,#ec4899);">Invite all →</button>
                </div>
            </div>

        {{-- ─────────── VARIANT 2 · Analytics ─────────── --}}
        @else
            <div class="p-5">
                <div class="text-xs font-bold uppercase tracking-widest text-violet-700">Analytics</div>
                <div class="text-sm font-black text-slate-900">Last 30 days · attributed revenue</div>

                <div class="mt-3 grid grid-cols-3 gap-2">
                    <div class="rounded-lg bg-gradient-to-br from-violet-500 to-pink-500 p-2 text-white">
                        <div class="text-[9px] uppercase tracking-widest opacity-80">Revenue</div>
                        <div class="text-lg font-black">₹4.2L</div>
                    </div>
                    <div class="rounded-lg bg-gradient-to-br from-cyan-500 to-emerald-500 p-2 text-white">
                        <div class="text-[9px] uppercase tracking-widest opacity-80">Orders</div>
                        <div class="text-lg font-black">1,284</div>
                    </div>
                    <div class="rounded-lg bg-slate-950 p-2 text-white">
                        <div class="text-[9px] uppercase tracking-widest opacity-80">ROAS</div>
                        <div class="text-lg font-black text-emerald-400">6.8×</div>
                    </div>
                </div>

                {{-- Fake chart --}}
                <div class="mt-4 flex items-end gap-1 rounded-xl bg-slate-50 p-3" style="height:120px;">
                    @foreach([22,30,26,42,38,55,48,60,52,68,74,66,82,90,84,70,76,88,94,80,72,66,58,50,62,74,80,86,92,98] as $h)
                        <div class="w-2 rounded-t" style="height: {{ $h }}%; background-image: linear-gradient(180deg,#a78bfa,#ec4899);"></div>
                    @endforeach
                </div>

                <div class="mt-4 grid grid-cols-3 gap-2 text-[10px]">
                    @foreach([['@aria.k','₹1.8L','98'], ['@mira.reels','₹1.2L','92'], ['@theovlog','₹80K','88']] as $r)
                        <div class="rounded-lg border border-slate-100 p-2">
                            <div class="font-bold text-slate-900">{{ $r[0] }}</div>
                            <div class="mt-0.5 flex items-center justify-between">
                                <span class="text-emerald-600 font-bold">{{ $r[1] }}</span>
                                <span class="rounded bg-violet-100 px-1 text-[9px] text-violet-700 font-bold">{{ $r[2] }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    {{-- Floating chip badges --}}
    <div class="absolute -left-4 top-6 hidden rounded-2xl bg-white p-3 shadow-lg ring-1 ring-slate-100 md:block">
        <div class="flex items-center gap-2 text-xs">
            <span class="grid h-6 w-6 place-items-center rounded-lg bg-emerald-100 text-emerald-700">✓</span>
            <span class="font-semibold text-slate-800">
                @if($variant === 0) 3 approved
                @elseif($variant === 1) 98% match
                @else +34% MoM
                @endif
            </span>
        </div>
    </div>
    <div class="absolute -right-3 -bottom-3 hidden rounded-2xl bg-white p-3 shadow-lg ring-1 ring-slate-100 md:block">
        <div class="flex items-center gap-2 text-xs">
            <span class="grid h-6 w-6 place-items-center rounded-lg bg-violet-100 text-violet-700">
                @if($variant === 0) 🚀
                @elseif($variant === 1) 🎯
                @else 💰
                @endif
            </span>
            <span class="font-semibold text-slate-800">
                @if($variant === 0) Auto-invited
                @elseif($variant === 1) AI shortlisted
                @else INR payout
                @endif
            </span>
        </div>
    </div>
</div>
