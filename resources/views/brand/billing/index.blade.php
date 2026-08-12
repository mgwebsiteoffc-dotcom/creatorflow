<x-layouts.app panel="brand" title="Billing">
    @php $sym = \App\Models\Workspace::symbolFor($workspace->currency); @endphp

    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <h1 class="text-3xl font-black tracking-tight text-slate-900">Billing</h1>
            <p class="mt-1 text-sm text-slate-500">Payments, invoices, and receipts — all in one place.</p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('brand.escrow.top-up') }}" class="btn-gradient !py-2 !text-xs">
                <x-icon name="plus" class="h-4 w-4" /> Add funds to escrow
            </a>
            <div class="hidden text-right sm:block">
                <div class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Currency</div>
                <div class="text-sm font-bold">{{ $sym }} · {{ $workspace->currency }}</div>
            </div>
        </div>
    </div>

    <div class="mt-6 flex flex-wrap gap-2">
        <a href="{{ route('brand.settings.profile') }}" class="tab-pill">Profile</a>
        <a href="{{ route('brand.settings.team') }}" class="tab-pill">Team</a>
        <a href="{{ route('brand.billing.index') }}" class="tab-pill is-active">Billing</a>
    </div>

    @if(! empty($schemaMissing))
        <div class="mt-6 flex items-start gap-3 rounded-2xl border border-amber-300 bg-amber-50 p-4 text-sm text-amber-900">
            <span class="grid h-9 w-9 shrink-0 place-items-center rounded-xl bg-gradient-to-br from-amber-500 to-orange-500 text-white shadow-sm"><x-icon name="alert" class="h-5 w-5 text-white" /></span>
            <div class="flex-1">
                <p class="font-bold">Billing schema hasn't been migrated yet.</p>
                <p class="mt-1 text-xs text-amber-800">Run
                    <code class="rounded bg-white/70 px-1.5 py-0.5">php artisan migrate</code>
                    from the project root to create the <code class="rounded bg-white/70 px-1.5 py-0.5">payment_records</code>table.
                    Until then, this page shows an empty state and the "Record a payment" form is disabled.
                </p>
            </div>
        </div>
    @endif

    {{-- Totals --}}
    <div class="mt-8 grid gap-4 md:grid-cols-4">
        <x-stat label="Paid this month" :value="$workspace->formatMoney($totals['paid_this_month'])" tone="violet"/>
        <x-stat label="Lifetime spend" :value="$workspace->formatMoney($totals['paid_lifetime'])" tone="emerald"/>
        <x-stat label="Refunded" :value="$workspace->formatMoney($totals['refunded'])" tone="rose"/>
        <x-stat label="Pending" :value="$workspace->formatMoney($totals['pending'])" tone="amber"/>
    </div>

    {{-- Plan card --}}
    <div class="mt-8 overflow-hidden rounded-2xl border border-slate-200 bg-white">
        <div class="grid gap-3 p-6 md:grid-cols-3 md:items-center">
            <div>
                <p class="text-xs font-bold uppercase tracking-widest text-slate-500">Current plan</p>
                <p class="mt-1 text-2xl font-black capitalize text-slate-900">{{ $workspace->plan }}</p>
                <p class="text-xs text-slate-500">{{ $workspace->plan_status }}</p>
            </div>
            <div>
                <p class="text-xs font-bold uppercase tracking-widest text-slate-500">Billed to</p>
                <p class="mt-1 text-sm font-medium">{{ $workspace->legal_name ?: $workspace->name }}</p>
                <p class="text-xs text-slate-500">{{ $workspace->contact_email ?: '—' }}</p>
            </div>
            <div class="md:text-right">
                <a href="{{ route('pricing') }}" class="btn-secondary !py-2 text-sm">Change plan</a>
                <a href="{{ route('brand.settings.profile') }}" class="btn-primary !py-2 ml-1 text-sm">Update billing details</a>
            </div>
        </div>
    </div>

    <div class="mt-10 grid gap-6 lg:grid-cols-3">
        {{-- Payment history --}}
        <section class="lg:col-span-2 rounded-2xl border border-slate-200 bg-white p-6">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <h2 class="text-lg font-bold text-slate-900">Payment history</h2>
                <div class="flex flex-wrap gap-2">
                    @foreach(['all' => 'All', 'campaign' => 'Campaign', 'subscription' => 'Subscription', 'refund' => 'Refunds'] as $k => $l)
                        <a href="{{ route('brand.billing.index', ['kind' => $k]) }}" class="tab-pill !py-1 !px-3 !text-xs {{ $filter === $k ? 'is-active' : '' }}">{{ $l }}</a>
                    @endforeach
                </div>
            </div>

            <div class="mt-4 overflow-hidden rounded-xl border border-slate-100">
                <table class="w-full text-sm">
                    <thead class="border-b border-slate-100 bg-slate-50 text-left text-xs uppercase tracking-widest text-slate-500">
                        <tr>
                            <th class="p-3">When</th>
                            <th class="p-3">Description</th>
                            <th class="p-3">Method</th>
                            <th class="p-3 text-right">Amount</th>
                            <th class="p-3 text-right">Status</th>
                            <th class="p-3 text-right">Receipt</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($payments as $p)
                            <tr>
                                <td class="p-3 text-xs text-slate-500">{{ optional($p->paid_at ?: $p->created_at)->format('M j, Y') }}</td>
                                <td class="p-3">
                                    <div class="font-semibold text-slate-900">{{ $p->description ?: ucfirst($p->kind) }}</div>
                                    @if($p->reference)<div class="text-[11px] text-slate-500">{{ $p->reference }}</div>@endif
                                    @if($p->campaign)<div class="text-[11px] text-slate-500">Campaign: {{ $p->campaign->title }}</div>@endif
                                </td>
                                <td class="p-3 uppercase text-xs text-slate-500">{{ $p->method ?: '—' }}</td>
                                <td class="p-3 text-right font-mono text-sm font-semibold {{ $p->direction === 'inflow' ? 'text-emerald-600' : 'text-slate-900' }}">
                                    {{ $p->direction === 'inflow' ? '+' : '−' }}{{ $workspace->formatMoney($p->amount_cents, $p->currency) }}
                                </td>
                                <td class="p-3 text-right">
                                    <x-badge :tone="$p->status === 'succeeded' ? 'green' : ($p->status === 'failed' ? 'rose' : ($p->status === 'refunded' ? 'sky' : 'amber'))">
                                        {{ $p->status }}
                                    </x-badge>
                                </td>
                                <td class="p-3 text-right">
                                    <div class="inline-flex items-center gap-2">
                                        @if($p->status === 'succeeded')
                                            <a href="{{ route('brand.billing.invoice', $p) }}" target="_blank"
                                               class="inline-flex items-center gap-1 text-xs font-semibold text-violet-700 hover:text-violet-900" title="Open tax invoice">
                                                <x-icon name="download" class="h-3.5 w-3.5" /> Tax invoice
                                            </a>
                                        @endif
                                        @if($p->receipt_url)
                                            <a class="text-xs font-semibold text-slate-500 hover:text-slate-900" href="{{ $p->receipt_url }}" target="_blank">Receipt ↗</a>
                                        @endif
                                        @if($p->status !== 'succeeded' && ! $p->receipt_url) — @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="p-8 text-center text-sm text-slate-500">
                                No payments recorded yet.<br>
                                Subscription charges flow in automatically. Use the form on the right to log a campaign payment.
                            </td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">{{ $payments->links() }}</div>
        </section>

        {{-- Right rail: record payment + invoices --}}
        <aside class="space-y-6">
            <div class="rounded-2xl border border-slate-200 bg-white p-6">
                <h3 class="text-base font-bold text-slate-900">Record a payment</h3>
                <p class="mt-1 text-xs text-slate-500">Log a manual or bank-transfer payment for your records.</p>
                <form method="POST" action="{{ route('brand.billing.payments.store') }}" class="mt-4 space-y-3">
                    @csrf
                    <div>
                        <label class="label">Description</label>
                        <input class="input" name="description" placeholder="Creator fee · Priya (Reel)" required>
                    </div>
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="label">Kind</label>
                            <select name="kind" class="input">
                                <option value="campaign">Campaign</option>
                                <option value="subscription">Subscription</option>
                                <option value="top_up">Top-up</option>
                                <option value="refund">Refund</option>
                                <option value="adjustment">Adjustment</option>
                            </select>
                        </div>
                        <div>
                            <label class="label">Method</label>
                            <select name="method" class="input">
                                <option value="manual">Manual</option>
                                <option value="card">Card</option>
                                <option value="upi">UPI</option>
                                <option value="bank">Bank transfer</option>
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="label">Amount (₹)</label>
                        <input class="input" type="number" step="0.01" name="amount" min="0.01" required placeholder="e.g. 1500.00">
                    </div>
                    <div>
                        <label class="label">Reference (optional)</label>
                        <input class="input" name="reference" placeholder="UPI ref / bank txn id">
                    </div>
                    <div>
                        <label class="label">Receipt URL (optional)</label>
                        <input class="input" type="url" name="receipt_url" placeholder="https://…">
                    </div>
                    <div>
                        <label class="label">Paid on</label>
                        <input class="input" type="date" name="paid_at" value="{{ now()->format('Y-m-d') }}">
                    </div>
                    <button class="btn-primary w-full" @if(! empty($schemaMissing)) disabled title="Run php artisan migrate first" @endif>Save record</button>
                </form>
            </div>

            <div class="rounded-2xl border border-slate-200 bg-white p-6">
                <h3 class="text-base font-bold text-slate-900">Invoices</h3>
                @if($invoices->isEmpty())
                    <p class="mt-2 text-sm text-slate-500">Subscription invoices from Stripe / Shopify appear here.</p>
                @else
                    <div class="mt-3 space-y-2">
                        @foreach($invoices as $inv)
                            <div class="flex items-center justify-between rounded-xl border border-slate-100 p-3 text-sm">
                                <div>
                                    <div class="font-semibold text-slate-900">{{ $workspace->formatMoney($inv->amount_cents, $inv->currency) }}</div>
                                    <div class="text-[11px] text-slate-500">{{ optional($inv->paid_at ?: $inv->due_at)->format('M j, Y') }} · {{ $inv->provider }}</div>
                                </div>
                                @if($inv->pdf_path)
                                    <a href="{{ $inv->pdf_path }}" target="_blank" class="text-xs font-semibold text-violet-700 hover:text-violet-900">PDF ↗</a>
                                @else
                                    <x-badge :tone="$inv->status === 'paid' ? 'green' : 'amber'">{{ $inv->status }}</x-badge>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </aside>
    </div>
</x-layouts.app>
