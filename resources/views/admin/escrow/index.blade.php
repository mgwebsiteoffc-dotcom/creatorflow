<x-layouts.admin title="Escrow">
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <h1 class="text-3xl font-black tracking-tight text-slate-900">Escrow &amp; payouts</h1>
            <p class="mt-1 text-sm text-slate-500">Hold, release, and refund creator funds. Full ledger below.</p>
        </div>
    </div>

    @if(! empty($schemaMissing))
        <div class="mt-6 flex items-start gap-3 rounded-2xl border border-amber-300 bg-amber-50 p-4 text-sm text-amber-900">
            <span class="grid h-9 w-9 shrink-0 place-items-center rounded-xl bg-gradient-to-br from-amber-500 to-orange-500 text-white shadow-sm">⚠</span>
            <div class="flex-1"><p class="font-bold">escrow_transactions table not migrated yet.</p><p class="mt-1 text-xs text-amber-800">Run <code class="rounded bg-white/70 px-1.5 py-0.5">php artisan migrate</code> to enable holds, refunds and the ledger.</p></div>
        </div>
    @endif

    <div class="mt-8 grid gap-4 md:grid-cols-4">
        <x-stat label="Currently held" :value="'₹'.number_format($totals['held']/100, 2, '.', ',')" tone="amber"/>
        <x-stat label="Released" :value="'₹'.number_format($totals['released']/100, 2, '.', ',')" tone="emerald"/>
        <x-stat label="Refunded" :value="'₹'.number_format($totals['refunded']/100, 2, '.', ',')" tone="rose"/>
        <x-stat label="Fees collected" :value="'₹'.number_format($totals['fees']/100, 2, '.', ',')" tone="violet"/>
    </div>

    <div class="mt-8 rounded-2xl border border-slate-200 bg-white p-6">
        <div class="flex items-center justify-between">
            <h2 class="text-lg font-bold text-slate-900">Pending payouts</h2>
            <span class="text-xs text-slate-500">{{ $pendingPayouts->count() }} awaiting release</span>
        </div>
        <div class="mt-4 space-y-2">
            @forelse($pendingPayouts as $p)
                <div class="flex items-center gap-3 rounded-xl border border-slate-100 p-3">
                    <div class="grid h-10 w-10 place-items-center rounded-full bg-gradient-to-br from-emerald-500 to-teal-500 text-white">₹</div>
                    <div class="min-w-0 flex-1">
                        <p class="truncate text-sm font-semibold text-slate-900">₹{{ number_format($p->net_cents/100, 2, '.', ',') }} → {{ $p->creator?->display_name ?? 'creator' }}</p>
                        <p class="text-xs text-slate-500">Assignment #{{ $p->assignment_id }} · created {{ $p->created_at->diffForHumans() }}</p>
                    </div>
                    <form method="POST" action="{{ route('admin.escrow.release', $p) }}" data-confirm="Release ₹{{ number_format($p->net_cents/100, 2, '.', ',') }} to creator?">@csrf
                        <button class="btn-primary !py-1.5 text-xs">Release now</button>
                    </form>
                </div>
            @empty
                <p class="text-sm text-slate-500">No pending payouts right now.</p>
            @endforelse
        </div>
    </div>

    <div class="mt-6 grid gap-6 lg:grid-cols-2">
        <div class="rounded-2xl border border-slate-200 bg-white p-6">
            <h3 class="text-base font-bold text-slate-900">Hold funds manually</h3>
            <form method="POST" action="{{ route('admin.escrow.hold') }}" class="mt-3 space-y-3">
                @csrf
                <div><label class="label">Assignment ID</label><input class="input" name="assignment_id" type="number" required></div>
                <div><label class="label">Amount (₹)</label><input class="input" name="amount" type="number" step="0.01" min="0.01" required placeholder="e.g. 5000.00"></div>
                <div><label class="label">Note</label><input class="input" name="note" placeholder="Reason for hold"></div>
                <button class="btn-primary w-full">Record hold</button>
            </form>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-6">
            <h3 class="text-base font-bold text-slate-900">Refund to brand</h3>
            <form method="POST" action="{{ route('admin.escrow.refund') }}" class="mt-3 space-y-3">
                @csrf
                <div><label class="label">Assignment ID</label><input class="input" name="assignment_id" type="number" required></div>
                <div><label class="label">Amount (₹)</label><input class="input" name="amount" type="number" step="0.01" min="0.01" required placeholder="e.g. 5000.00"></div>
                <div><label class="label">Note</label><input class="input" name="note" placeholder="Reason for refund"></div>
                <button class="btn-danger w-full">Refund</button>
            </form>
        </div>
    </div>

    <h2 class="mt-10 text-lg font-bold text-slate-900">Ledger</h2>
    <div class="mt-4 overflow-x-auto rounded-2xl border border-slate-200 bg-white">
        <table class="w-full min-w-[640px] text-sm">
            <thead class="border-b border-slate-200 bg-slate-50 text-left text-xs uppercase tracking-widest text-slate-500">
                <tr><th class="p-3">When</th><th class="p-3">Kind</th><th class="p-3">Amount</th><th class="p-3">Workspace</th><th class="p-3">Creator</th><th class="p-3">Note</th></tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($transactions as $t)
                    <tr>
                        <td class="p-3 text-xs text-slate-500">{{ $t->created_at->format('M j, H:i') }}</td>
                        <td class="p-3"><x-badge :tone="match($t->kind){'hold'=>'amber','release'=>'green','refund'=>'rose','fee'=>'violet','markup'=>'sky',default=>'slate'}">{{ $t->kind }}</x-badge></td>
                        <td class="p-3 font-semibold">₹{{ number_format($t->amount_cents/100, 2, '.', ',') }}</td>
                        <td class="p-3 text-slate-600">{{ $t->workspace?->name ?? '—' }}</td>
                        <td class="p-3 text-slate-600">{{ $t->creator?->display_name ?? '—' }}</td>
                        <td class="p-3 text-xs text-slate-500">{{ $t->note }}</td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="p-6 text-center text-sm text-slate-500">No transactions yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">{{ $transactions->links() }}</div>
</x-layouts.admin>
