<x-layouts.app panel="brand" title="Analytics">
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <h1 class="text-3xl font-black tracking-tight text-slate-900">Analytics &amp; attribution</h1>
            <p class="mt-1 text-sm text-slate-500">Revenue tied to creators via unique codes, referral links and multi-touch attribution.</p>
        </div>
    </div>

    <form method="GET" class="card mt-6 flex flex-wrap items-end gap-3 p-5">
        <div class="min-w-[220px] flex-1">
            <label class="label">Campaign</label>
            <select class="input" name="campaign_id">
                <option value="">All campaigns</option>
                @foreach($campaigns as $c)
                    <option value="{{ $c->id }}" @selected($campaignId == $c->id)>{{ $c->title }}</option>
                @endforeach
            </select>
        </div>
        <button class="btn-primary">Apply</button>
    </form>

    <div class="mt-5 grid grid-cols-2 gap-3 md:grid-cols-3">
        <x-stat label="Attributed revenue" :value="$currentWorkspace->formatMoney($attributedRevenueCents)" tone="emerald"/>
        <x-stat label="Attributed orders" :value="$attributedOrders" tone="violet"/>
        <x-stat label="Active creators" :value="$creatorLeaderboard->count()" tone="sky"/>
    </div>

    <div class="mt-6 grid gap-5 lg:grid-cols-2">
        <div class="card p-5">
            <h2 class="font-semibold">Revenue over time</h2>
            @if($daily->isEmpty())
                <p class="mt-4 text-sm text-slate-500">No attributed orders yet. As creators post and customers check out, this chart fills in.</p>
            @else
                @php $max = $daily->max('revenue_cents') ?: 1; @endphp
                <div class="mt-4 flex h-40 items-end gap-1.5">
                    @foreach($daily as $d)
                        <div class="group flex-1">
                            <div class="rounded-t bg-gradient-to-t from-violet-500 to-cyan-400" style="height: {{ max(4, ($d->revenue_cents / $max) * 100) }}%"></div>
                        </div>
                    @endforeach
                </div>
                <p class="mt-2 text-xs text-slate-500">Last {{ $daily->count() }} active days</p>
            @endif
        </div>

        <div class="card p-5">
            <h2 class="font-semibold">Top creators</h2>
            <div class="mt-3 space-y-2">
                @forelse($creatorLeaderboard as $row)
                    <div class="flex items-center justify-between text-sm">
                        <span>{{ $row->creator->display_name ?? 'Creator #'.$row->creator_id }}</span>
                        <span class="font-semibold text-emerald-600">{{ $currentWorkspace->formatMoney((int)($row->revenue ?? 0)) }} · {{ $row->orders }} orders</span>
                    </div>
                @empty
                    <p class="text-sm text-slate-500">No creator revenue yet.</p>
                @endforelse
            </div>
        </div>
    </div>
</x-layouts.app>
