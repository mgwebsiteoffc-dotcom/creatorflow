<x-layouts.admin title="Billing">
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <h1 class="text-3xl font-black tracking-tight text-slate-900">Billing &amp; invoices</h1>
            <p class="mt-1 text-sm text-slate-500">Every payment in, every invoice out, every payout to creators — system-wide.</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('admin.escrow.index') }}" class="btn-secondary !py-2 text-sm">🔒 Escrow</a>
            <a href="{{ route('admin.settings') }}"    class="btn-secondary !py-2 text-sm">⚙️ Fee settings</a>
        </div>
    </div>

    @if(! $hasPayments || ! $hasInvoices)
        <div class="mt-6 flex items-start gap-3 rounded-2xl border border-amber-300 bg-amber-50 p-4 text-sm text-amber-900">
            <span class="grid h-9 w-9 shrink-0 place-items-center rounded-xl bg-gradient-to-br from-amber-500 to-orange-500 text-white shadow-sm">⚠</span>
            <div class="flex-1">
                <p class="font-bold">Some billing tables are missing.</p>
                <p class="mt-1 text-xs text-amber-800">
                    Run <code class="rounded bg-white/70 px-1.5 py-0.5">php artisan migrate</code> to enable
                    @if(! $hasPayments) <code class="rounded bg-white/70 px-1 py-0.5">payment_records</code>@endif
                    @if(! $hasPayments && ! $hasInvoices) and @endif
                    @if(! $hasInvoices) <code class="rounded bg-white/70 px-1 py-0.5">invoices</code>@endif.
                </p>
            </div>
        </div>
    @endif

    {{-- KPI STRIP --}}
    <div class="mt-8 grid gap-4 md:grid-cols-3 lg:grid-cols-5">
        <div class="card p-5">
            <p class="text-[11px] font-bold uppercase tracking-widest text-slate-500">Revenue collected</p>
            <p class="mt-2 text-3xl font-black text-emerald-600">${{ number_format($totals['inflow']/100, 0) }}</p>
        </div>
        <div class="card p-5">
            <p class="text-[11px] font-bold uppercase tracking-widest text-slate-500">Refunded</p>
            <p class="mt-2 text-3xl font-black text-rose-600">${{ number_format($totals['refunded']/100, 0) }}</p>
        </div>
        <div class="card p-5">
            <p class="text-[11px] font-bold uppercase tracking-widest text-slate-500">Pending</p>
            <p class="mt-2 text-3xl font-black text-amber-600">${{ number_format($totals['pending']/100, 0) }}</p>
        </div>
        <div class="card p-5">
            <p class="text-[11px] font-bold uppercase tracking-widest text-slate-500">Paid to creators</p>
            <p class="mt-2 text-3xl font-black text-slate-900">${{ number_format($payoutTotals['paid']/100, 0) }}</p>
        </div>
        <div class="card p-5">
            <p class="text-[11px] font-bold uppercase tracking-widest text-slate-500">Pending payouts</p>
            <p class="mt-2 text-3xl font-black text-slate-900">${{ number_format($payoutTotals['pending']/100, 0) }}</p>
        </div>
    </div>

    <div class="mt-10 grid gap-6 lg:grid-cols-3">
        {{-- PAYMENTS --}}
        <section class="lg:col-span-2 rounded-2xl border border-slate-200 bg-white p-6">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <h2 class="text-lg font-bold text-slate-900">Recent payments</h2>
                <div class="flex flex-wrap gap-2">
                    @foreach(['all'=>'All','campaign'=>'Campaign','subscription'=>'Subscription','refund'=>'Refunds'] as $k=>$l)
                        <a href="{{ route('admin.billing.index', ['kind'=>$k]) }}" class="tab-pill !py-1 !px-3 !text-xs {{ $filter===$k ? 'is-active' : '' }}">{{ $l }}</a>
                    @endforeach
                </div>
            </div>
            <div class="mt-4 overflow-hidden rounded-xl border border-slate-100">
                <table class="w-full text-sm">
                    <thead class="border-b border-slate-100 bg-slate-50 text-left text-xs uppercase tracking-widest text-slate-500">
                        <tr><th class="p-3">When</th><th class="p-3">Workspace</th><th class="p-3">Description</th><th class="p-3 text-right">Amount</th><th class="p-3 text-right">Status</th></tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($payments as $p)
                            <tr>
                                <td class="p-3 text-xs text-slate-500">{{ optional($p->paid_at ?: $p->created_at)->format('M j') }}</td>
                                <td class="p-3">
                                    @if($p->workspace)
                                        <a href="{{ route('admin.workspaces.show', $p->workspace) }}" class="font-semibold text-slate-800 hover:text-violet-700">{{ $p->workspace->name }}</a>
                                    @else — @endif
                                </td>
                                <td class="p-3">
                                    <div class="text-sm">{{ $p->description ?: ucfirst($p->kind) }}</div>
                                    @if($p->campaign)<div class="text-[11px] text-slate-500">{{ $p->campaign->title }}</div>@endif
                                </td>
                                <td class="p-3 text-right font-mono font-semibold {{ $p->direction === 'inflow' ? 'text-emerald-600' : 'text-slate-900' }}">
                                    {{ $p->direction === 'inflow' ? '+' : '−' }}{{ ($p->workspace ?? new \App\Models\Workspace)->formatMoney((int) $p->amount_cents, $p->currency) }}
                                </td>
                                <td class="p-3 text-right"><x-badge :tone="$p->status === 'succeeded' ? 'green' : ($p->status === 'refunded' ? 'sky' : 'amber')">{{ $p->status }}</x-badge></td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="p-6 text-center text-sm text-slate-500">No payments recorded yet.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $payments->links() }}</div>
        </section>

        {{-- SIDEBAR: TOP WORKSPACES + PAYOUTS + INVOICES --}}
        <aside class="space-y-6">
            <section class="rounded-2xl border border-slate-200 bg-white p-6">
                <h3 class="text-base font-bold text-slate-900">Top workspaces (by spend)</h3>
                <div class="mt-4 space-y-2">
                    @forelse($topWorkspaces as $row)
                        <a href="{{ route('admin.workspaces.show', $row->workspace) }}" class="flex items-center justify-between rounded-xl border border-slate-100 p-3 text-sm hover:border-violet-300">
                            <span class="font-semibold text-slate-800">{{ $row->workspace->name }}</span>
                            <span class="font-mono font-bold text-slate-900">{{ $row->workspace->formatMoney($row->total) }}</span>
                        </a>
                    @empty
                        <p class="text-xs text-slate-500">No paid workspaces yet.</p>
                    @endforelse
                </div>
            </section>

            <section class="rounded-2xl border border-slate-200 bg-white p-6">
                <h3 class="text-base font-bold text-slate-900">Recent payouts to creators</h3>
                <div class="mt-4 space-y-2">
                    @forelse($payouts as $p)
                        <div class="flex items-center justify-between rounded-xl border border-slate-100 p-3 text-sm">
                            <div>
                                <div class="font-semibold text-slate-800">{{ $p->creator?->display_name ?? 'creator' }}</div>
                                <div class="text-[11px] text-slate-500">{{ $p->workspace?->name ?? '—' }} · {{ $p->created_at->diffForHumans() }}</div>
                            </div>
                            <div class="text-right">
                                <div class="font-mono font-bold text-slate-900">${{ number_format($p->net_cents/100, 2) }}</div>
                                <x-badge :tone="$p->status === 'paid' ? 'green' : ($p->status === 'pending' ? 'amber' : 'slate')">{{ $p->status }}</x-badge>
                            </div>
                        </div>
                    @empty
                        <p class="text-xs text-slate-500">No payouts yet.</p>
                    @endforelse
                </div>
            </section>

            <section class="rounded-2xl border border-slate-200 bg-white p-6">
                <h3 class="text-base font-bold text-slate-900">Recent invoices</h3>
                <div class="mt-4 space-y-2">
                    @forelse($invoices as $inv)
                        <div class="flex items-center justify-between rounded-xl border border-slate-100 p-3 text-sm">
                            <div>
                                <div class="font-semibold text-slate-800">{{ $inv->workspace?->name ?? '—' }}</div>
                                <div class="text-[11px] text-slate-500">{{ $inv->provider }} · {{ optional($inv->paid_at ?: $inv->due_at)->format('M j, Y') ?? '—' }}</div>
                            </div>
                            <div class="text-right">
                                <div class="font-mono font-bold text-slate-900">${{ number_format($inv->amount_cents/100, 2) }}</div>
                                <x-badge :tone="$inv->status === 'paid' ? 'green' : 'amber'">{{ $inv->status }}</x-badge>
                            </div>
                        </div>
                    @empty
                        <p class="text-xs text-slate-500">No invoices yet — brand subscriptions flow in from Stripe / Shopify webhooks.</p>
                    @endforelse
                </div>
            </section>
        </aside>
    </div>
</x-layouts.admin>
