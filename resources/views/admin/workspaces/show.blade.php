<x-layouts.admin :title="$workspace->name">
    <a href="{{ route('admin.workspaces.index') }}" class="text-sm text-slate-500 hover:text-slate-800">← Workspaces</a>

    <div class="mt-3 overflow-hidden rounded-3xl border border-slate-200 bg-white">
        <div class="relative h-24" style="background-image: linear-gradient(120deg,#0ea5e9 0%,#7c3aed 50%,#ec4899 110%);"></div>
        <div class="relative px-6 pb-6 pt-0">
            <div class="-mt-10 flex flex-wrap items-end justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="grid h-20 w-20 shrink-0 place-items-center rounded-2xl bg-white text-xl font-black text-slate-800 shadow-lg ring-4 ring-white">
                        @if($workspace->logo_path)
                            <img src="{{ $workspace->logo_path }}" class="h-full w-full rounded-2xl object-cover" alt="">
                        @else
                            {{ strtoupper(substr($workspace->name, 0, 2)) }}
                        @endif
                    </div>
                    <div class="pt-8">
                        <h1 class="text-2xl font-black tracking-tight text-slate-900">{{ $workspace->name }}</h1>
                        <p class="text-sm text-slate-500">{{ $workspace->legal_name ?: '—' }} · {{ $workspace->website ?: 'no website' }}</p>
                    </div>
                </div>
                <div class="flex flex-wrap gap-2 pt-8">
                    <x-badge tone="violet">Plan · {{ $workspace->plan }}</x-badge>
                    @if($workspace->account_status === 'suspended')
                        <x-badge tone="rose">Suspended</x-badge>
                    @else
                        <x-badge tone="green">Active</x-badge>
                    @endif
                </div>
            </div>

            <div class="mt-6 grid gap-3 md:grid-cols-5">
                <div class="rounded-2xl border border-slate-100 p-3">
                    <div class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Campaigns</div>
                    <div class="mt-1 text-2xl font-black text-slate-900">{{ $stats['campaigns'] }}</div>
                </div>
                <div class="rounded-2xl border border-slate-100 p-3">
                    <div class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Products</div>
                    <div class="mt-1 text-2xl font-black text-slate-900">{{ $stats['products'] }}</div>
                </div>
                <div class="rounded-2xl border border-slate-100 p-3">
                    <div class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Team</div>
                    <div class="mt-1 text-2xl font-black text-slate-900">{{ $stats['team'] }}</div>
                </div>
                <div class="rounded-2xl border border-slate-100 p-3">
                    <div class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Attributed GMV</div>
                    <div class="mt-1 text-lg font-black text-slate-900">{{ $workspace->formatMoney($stats['gmv']) }}</div>
                </div>
                <div class="rounded-2xl border border-slate-100 p-3">
                    <div class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Paid to platform</div>
                    <div class="mt-1 text-lg font-black text-slate-900">{{ $workspace->formatMoney($stats['paid']) }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Profile + Actions --}}
    <div class="mt-8 grid gap-6 lg:grid-cols-3">
        <section class="lg:col-span-2 rounded-2xl border border-slate-200 bg-white p-6">
            <h2 class="text-lg font-bold text-slate-900">Brand profile</h2>
            <div class="mt-4 grid gap-4 text-sm sm:grid-cols-2">
                <div><div class="text-xs text-slate-400">Contact email</div><div class="font-medium">{{ $workspace->contact_email ?: '—' }}</div></div>
                <div><div class="text-xs text-slate-400">Contact phone</div><div class="font-medium">{{ $workspace->contact_phone ?: '—' }}</div></div>
                <div><div class="text-xs text-slate-400">Currency</div><div class="font-medium">{{ $workspace->currency }}</div></div>
                <div><div class="text-xs text-slate-400">Country</div><div class="font-medium">{{ $workspace->country ?: '—' }}</div></div>
                <div class="sm:col-span-2"><div class="text-xs text-slate-400">Address</div>
                    <div class="font-medium">
                        {{ $workspace->address_line1 ?: '' }}
                        @if($workspace->address_line2) · {{ $workspace->address_line2 }} @endif
                        @if($workspace->address_city) · {{ $workspace->address_city }} @endif
                        @if($workspace->address_state) · {{ $workspace->address_state }} @endif
                        @if($workspace->address_postal) · {{ $workspace->address_postal }} @endif
                        @if(! $workspace->address_line1) — @endif
                    </div>
                </div>
                <div><div class="text-xs text-slate-400">Tax</div><div class="font-medium">{{ $workspace->tax_type ?: '—' }} · {{ $workspace->tax_id ?: '—' }}</div></div>
                <div><div class="text-xs text-slate-400">Plan status</div><div class="font-medium">{{ $workspace->plan_status }}</div></div>
            </div>
        </section>

        <section class="rounded-2xl border border-slate-200 bg-white p-6">
            <h2 class="text-lg font-bold text-slate-900">Actions</h2>
            <div class="mt-4 space-y-2">
                @if($workspace->account_status === 'suspended')
                    <form method="POST" action="{{ route('admin.workspaces.reinstate', $workspace) }}">@csrf
                        <button class="btn-primary w-full !py-1.5 text-xs">Reinstate workspace</button>
                    </form>
                @else
                    <form method="POST" action="{{ route('admin.workspaces.suspend', $workspace) }}" data-confirm="Suspend {{ $workspace->name }}?">@csrf
                        <input type="hidden" name="reason" value="Suspended by admin">
                        <button class="btn-ghost w-full !py-1.5 text-xs !text-rose-600 hover:!bg-rose-50">Suspend workspace</button>
                    </form>
                @endif
            </div>

            @if($workspace->subscription)
                <h3 class="mt-6 text-sm font-bold text-slate-800">Subscription</h3>
                <div class="mt-2 rounded-xl bg-slate-50 p-3 text-xs">
                    <div><span class="text-slate-500">Provider:</span> {{ $workspace->subscription->provider }}</div>
                    <div><span class="text-slate-500">Status:</span> {{ $workspace->subscription->status }}</div>
                    <div><span class="text-slate-500">Ends:</span> {{ optional($workspace->subscription->current_period_end)->format('M j, Y') ?? '—' }}</div>
                </div>
            @endif
        </section>
    </div>

    {{-- TEAM --}}
    <section class="mt-8 rounded-2xl border border-slate-200 bg-white p-6">
        <h2 class="text-lg font-bold text-slate-900">Team ({{ $workspace->users->count() }})</h2>
        <div class="mt-4 grid gap-3 sm:grid-cols-2 md:grid-cols-3">
            @foreach($workspace->users as $u)
                <a href="{{ route('admin.users.show', $u) }}" class="flex items-center gap-3 rounded-xl border border-slate-100 p-3 hover:border-violet-300">
                    <div class="grid h-9 w-9 place-items-center rounded-full bg-slate-100 font-bold text-slate-500">{{ strtoupper(substr($u->name, 0, 1)) }}</div>
                    <div class="min-w-0 flex-1">
                        <div class="truncate text-sm font-semibold text-slate-900">{{ $u->name }}</div>
                        <div class="truncate text-xs text-slate-500">{{ $u->email }} · {{ $u->pivot->role }}</div>
                    </div>
                </a>
            @endforeach
        </div>
    </section>

    {{-- CAMPAIGNS --}}
    <section class="mt-8 rounded-2xl border border-slate-200 bg-white p-6">
        <h2 class="text-lg font-bold text-slate-900">Recent campaigns</h2>
        <div class="mt-4 overflow-x-auto rounded-xl border border-slate-100">
            <table class="w-full min-w-[640px] text-sm">
                <thead class="border-b border-slate-100 bg-slate-50 text-left text-xs uppercase tracking-widest text-slate-500">
                    <tr><th class="p-3">Title</th><th class="p-3">Type</th><th class="p-3">Creators</th><th class="p-3">Products</th><th class="p-3 text-right">Status</th></tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($campaigns as $c)
                        <tr>
                            <td class="p-3 font-semibold text-slate-800">{{ $c->title }}</td>
                            <td class="p-3 text-slate-600 capitalize">{{ $c->type }}</td>
                            <td class="p-3 text-slate-600">{{ $c->assignments_count }}</td>
                            <td class="p-3 text-slate-600">{{ $c->products_count }}</td>
                            <td class="p-3 text-right"><x-badge :tone="in_array($c->status, ['active','completed']) ? 'green' : ($c->status === 'cancelled' ? 'rose' : 'amber')">{{ ucfirst($c->status) }}</x-badge></td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="p-6 text-center text-sm text-slate-500">No campaigns yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    {{-- PRODUCTS --}}
    @if($products->isNotEmpty())
        <section class="mt-8 rounded-2xl border border-slate-200 bg-white p-6">
            <h2 class="text-lg font-bold text-slate-900">Products</h2>
            <div class="mt-4 grid gap-3 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4">
                @foreach($products as $p)
                    <div class="rounded-xl border border-slate-100 p-3">
                        <div class="text-sm font-semibold text-slate-900 truncate">{{ $p->title }}</div>
                        <div class="text-xs text-slate-500">{{ $p->product_type ?: '—' }}</div>
                    </div>
                @endforeach
            </div>
        </section>
    @endif

    {{-- INVOICES --}}
    @if($invoices->isNotEmpty())
        <section class="mt-8 rounded-2xl border border-slate-200 bg-white p-6">
            <h2 class="text-lg font-bold text-slate-900">Invoices</h2>
            <div class="mt-4 overflow-x-auto rounded-xl border border-slate-100">
                <table class="w-full min-w-[640px] text-sm">
                    <thead class="border-b border-slate-100 bg-slate-50 text-left text-xs uppercase tracking-widest text-slate-500">
                        <tr><th class="p-3">Provider</th><th class="p-3">Date</th><th class="p-3 text-right">Amount</th><th class="p-3 text-right">Status</th><th class="p-3 text-right">PDF</th></tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($invoices as $inv)
                            <tr>
                                <td class="p-3 uppercase text-xs text-slate-500">{{ $inv->provider }}</td>
                                <td class="p-3 text-xs text-slate-500">{{ optional($inv->paid_at ?: $inv->due_at)->format('M j, Y') ?? '—' }}</td>
                                <td class="p-3 text-right font-mono font-semibold text-slate-900">{{ $workspace->formatMoney((int) $inv->amount_cents, $inv->currency) }}</td>
                                <td class="p-3 text-right"><x-badge :tone="$inv->status === 'paid' ? 'green' : ($inv->status === 'refunded' ? 'sky' : 'amber')">{{ $inv->status }}</x-badge></td>
                                <td class="p-3 text-right">@if($inv->pdf_path)<a href="{{ $inv->pdf_path }}" target="_blank" class="text-xs font-semibold text-violet-700">PDF ↗</a>@else — @endif</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>
    @endif

    {{-- MANUAL PAYMENT RECORDS --}}
    @if($payments->isNotEmpty())
        <section class="mt-8 rounded-2xl border border-slate-200 bg-white p-6">
            <h2 class="text-lg font-bold text-slate-900">Payment records</h2>
            <div class="mt-4 overflow-x-auto rounded-xl border border-slate-100">
                <table class="w-full min-w-[640px] text-sm">
                    <thead class="border-b border-slate-100 bg-slate-50 text-left text-xs uppercase tracking-widest text-slate-500">
                        <tr><th class="p-3">When</th><th class="p-3">Kind</th><th class="p-3">Description</th><th class="p-3 text-right">Amount</th><th class="p-3 text-right">Status</th></tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($payments as $p)
                            <tr>
                                <td class="p-3 text-xs text-slate-500">{{ optional($p->paid_at ?: $p->created_at)->format('M j, Y') }}</td>
                                <td class="p-3 uppercase text-xs text-slate-500">{{ $p->kind }}</td>
                                <td class="p-3">{{ $p->description ?: '—' }}</td>
                                <td class="p-3 text-right font-mono font-semibold {{ $p->direction === 'inflow' ? 'text-emerald-600' : 'text-slate-900' }}">
                                    {{ $p->direction === 'inflow' ? '+' : '−' }}{{ $workspace->formatMoney((int) $p->amount_cents, $p->currency) }}
                                </td>
                                <td class="p-3 text-right"><x-badge :tone="$p->status === 'succeeded' ? 'green' : ($p->status === 'refunded' ? 'sky' : 'amber')">{{ $p->status }}</x-badge></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>
    @endif
</x-layouts.admin>
