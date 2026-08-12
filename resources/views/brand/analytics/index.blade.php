<x-layouts.app panel="brand" title="Analytics">
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black tracking-tight text-slate-900 md:text-3xl">Analytics &amp; attribution</h1>
            <p class="mt-1 text-sm text-slate-500">Revenue tied to creators via unique codes, UTM tags, and Shopify order webhooks.</p>
        </div>
        <div class="text-xs font-semibold text-slate-500">
            Last <span class="tabular-nums">{{ $days }}</span> days
        </div>
    </div>

    {{-- ============================ FILTER BAR ============================ --}}
    <form method="GET" class="card mt-5 p-4">
        <div class="grid gap-2 md:grid-cols-12">
            <div class="md:col-span-6">
                <label class="label">Campaign</label>
                <select class="input" name="campaign_id">
                    <option value="">All campaigns</option>
                    @foreach($campaigns as $c)
                        <option value="{{ $c->id }}" @selected($campaignId == $c->id)>
                            {{ $c->title }}
                            @if($c->status) — {{ $c->status }} @endif
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="md:col-span-4">
                <label class="label">Date range</label>
                <select class="input" name="days">
                    @foreach([7 => 'Last 7 days', 14 => 'Last 14 days', 30 => 'Last 30 days', 60 => 'Last 60 days', 90 => 'Last 90 days', 180 => 'Last 180 days'] as $v => $l)
                        <option value="{{ $v }}" @selected($days == $v)>{{ $l }}</option>
                    @endforeach
                </select>
            </div>
            <div class="md:col-span-2 flex items-end">
                <button class="btn-primary w-full">Apply</button>
            </div>
        </div>
    </form>

    {{-- ============================ KPI BAND ============================ --}}
    @php
        $fmtDelta = function (float $pct): array {
            if ($pct === 0.0) return ['—', 'text-slate-500', 'bg-slate-100'];
            $up = $pct > 0;
            return [
                ($up ? '+' : '').number_format($pct, 1).'%',
                $up ? 'text-emerald-700' : 'text-rose-700',
                $up ? 'bg-emerald-100' : 'bg-rose-100',
            ];
        };
        [$revLabel, $revColor, $revBg]     = $fmtDelta($deltaRevenuePct);
        [$ordLabel, $ordColor, $ordBg]     = $fmtDelta($deltaOrdersPct);
    @endphp
    <div class="mt-5 grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
        <div class="rounded-2xl border border-slate-200 bg-white p-4">
            <div class="flex items-center justify-between">
                <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Attributed GMV</p>
                <span class="rounded-md px-1.5 py-0.5 text-[10px] font-semibold {{ $revBg }} {{ $revColor }}">{{ $revLabel }}</span>
            </div>
            <p class="mt-2 text-2xl font-black tracking-tight text-slate-900">
                {{ $currentWorkspace->formatMoney($totalRevenueCents) }}
            </p>
            <p class="mt-0.5 text-[11px] text-slate-500">vs previous {{ $days }}-day window</p>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-4">
            <div class="flex items-center justify-between">
                <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Attributed orders</p>
                <span class="rounded-md px-1.5 py-0.5 text-[10px] font-semibold {{ $ordBg }} {{ $ordColor }}">{{ $ordLabel }}</span>
            </div>
            <p class="mt-2 text-2xl font-black tracking-tight text-slate-900 tabular-nums">
                {{ number_format($totalOrders) }}
            </p>
            <p class="mt-0.5 text-[11px] text-slate-500">Unique creator-driven orders</p>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-4">
            <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Active creators</p>
            <p class="mt-2 text-2xl font-black tracking-tight text-slate-900 tabular-nums">
                {{ number_format($activeCreators) }}
            </p>
            <p class="mt-0.5 text-[11px] text-slate-500">Drove at least one attributed order</p>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-4">
            <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Avg order value</p>
            <p class="mt-2 text-2xl font-black tracking-tight text-slate-900">
                {{ $currentWorkspace->formatMoney($aov) }}
            </p>
            <p class="mt-0.5 text-[11px] text-slate-500">Across attributed orders</p>
        </div>
    </div>

    {{-- ============================ MAIN CHART ============================ --}}
    <div class="mt-5 grid gap-4 lg:grid-cols-3">
        <div class="lg:col-span-2 rounded-2xl border border-slate-200 bg-white p-5">
            <div class="flex items-center justify-between">
                <h2 class="text-sm font-bold text-slate-900">Revenue &amp; orders — last {{ $days }} days</h2>
                <div class="flex items-center gap-3 text-[11px] text-slate-500">
                    <span class="inline-flex items-center gap-1"><span class="h-2 w-2 rounded-sm bg-gradient-to-t from-violet-500 to-pink-500"></span> Revenue</span>
                    <span class="inline-flex items-center gap-1"><span class="h-2 w-2 rounded-full bg-emerald-500"></span> Orders</span>
                </div>
            </div>

            @php
                $maxRev = max(1, $daily->max('revenue'));
                $maxOrd = max(1, $daily->max('orders'));
            @endphp
            @if($daily->isEmpty() || $totalRevenueCents === 0)
                <div class="mt-6 flex h-48 flex-col items-center justify-center text-center">
                    <div class="grid h-12 w-12 place-items-center rounded-2xl bg-slate-100 text-slate-500"><x-icon name="chart-bar" class="h-6 w-6" /></div>
                    <p class="mt-3 text-sm font-semibold text-slate-700">No attributed revenue yet</p>
                    <p class="mt-1 max-w-xs text-xs text-slate-500">As creators post and customers check out, this chart fills in — usually 24-72 hours after your first campaign goes live.</p>
                </div>
            @else
                <div class="relative mt-5 h-48">
                    <div class="flex h-full items-end gap-[3px]">
                        @foreach($daily as $d)
                            @php $hRev = max(2, round(($d->revenue / $maxRev) * 100, 1)); @endphp
                            <div class="group relative flex-1" title="{{ $d->date->format('D, M j') }} · {{ $currentWorkspace->formatMoney($d->revenue) }} · {{ $d->orders }} orders">
                                <div class="rounded-t bg-gradient-to-t from-violet-500 to-pink-500 transition group-hover:from-violet-600 group-hover:to-pink-600" style="height: {{ $hRev }}%"></div>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="mt-2 flex justify-between text-[10px] text-slate-400">
                    <span>{{ $daily->first()->date->format('M j') }}</span>
                    <span>{{ $daily->last()->date->format('M j') }}</span>
                </div>
            @endif
        </div>

        {{-- Top creators leaderboard --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-5">
            <h2 class="text-sm font-bold text-slate-900">Top creators</h2>
            @if($creatorLeaderboard->isEmpty())
                <p class="mt-4 text-xs text-slate-500">No creator revenue in this window yet.</p>
            @else
                <ol class="mt-3 divide-y divide-slate-100">
                    @foreach($creatorLeaderboard as $i => $row)
                        @php $c = $row->creator; @endphp
                        <li class="flex items-center gap-3 py-2.5">
                            <span class="grid h-6 w-6 shrink-0 place-items-center rounded-md {{ $i === 0 ? 'bg-amber-100 text-amber-700' : 'bg-slate-100 text-slate-500' }} text-[11px] font-bold">
                                {{ $i + 1 }}
                            </span>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-semibold text-slate-900">
                                    @if($c)
                                        <a href="{{ route('brand.creators.show', $c) }}" class="hover:text-violet-700">{{ $c->display_name }}</a>
                                    @else
                                        Creator #{{ $row->creator_id }}
                                    @endif
                                </p>
                                <p class="text-[11px] text-slate-500 tabular-nums">
                                    {{ $row->orders }} order{{ $row->orders === 1 ? '' : 's' }}
                                    @if($c && $c->engagement_rate)
                                        · {{ number_format((float) $c->engagement_rate, 1) }}% ER
                                    @endif
                                </p>
                            </div>
                            <span class="text-sm font-black text-emerald-600 tabular-nums">
                                {{ $currentWorkspace->formatMoney((int) ($row->revenue ?? 0)) }}
                            </span>
                        </li>
                    @endforeach
                </ol>
            @endif
        </div>
    </div>

    {{-- ============================ ROAS PER CAMPAIGN ============================ --}}
    <div class="mt-5 rounded-2xl border border-slate-200 bg-white p-5">
        <div class="flex items-center justify-between">
            <h2 class="text-sm font-bold text-slate-900">ROAS by campaign</h2>
            <span class="text-[11px] text-slate-500">Revenue ÷ (product cost + fees + commissions)</span>
        </div>

        @if($campaignRoas->isEmpty())
            <p class="mt-4 text-xs text-slate-500">Roll-ups arrive after the first attributed order per campaign.</p>
        @else
            <div class="mt-4 overflow-x-auto">
                <table class="w-full min-w-[640px] text-sm">
                    <thead class="border-b border-slate-100 text-left text-[10px] font-bold uppercase tracking-widest text-slate-400">
                        <tr>
                            <th class="py-2">Campaign</th>
                            <th class="py-2 text-right">Content</th>
                            <th class="py-2 text-right">Orders</th>
                            <th class="py-2 text-right">Revenue</th>
                            <th class="py-2 text-right">Cost</th>
                            <th class="py-2 text-right">ROAS</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($campaignRoas as $r)
                            <tr class="text-sm">
                                <td class="py-2.5 font-medium text-slate-900">
                                    @if($r->campaign)
                                        <a href="{{ route('brand.campaigns.show', $r->campaign) }}" class="hover:text-violet-700">{{ $r->campaign->title }}</a>
                                    @else
                                        Campaign #{{ $r->campaign_id }}
                                    @endif
                                </td>
                                <td class="py-2.5 text-right text-slate-600 tabular-nums">{{ number_format($r->content_count) }}</td>
                                <td class="py-2.5 text-right text-slate-600 tabular-nums">{{ number_format($r->orders) }}</td>
                                <td class="py-2.5 text-right font-semibold text-slate-900 tabular-nums">{{ $currentWorkspace->formatMoney((int) $r->revenue) }}</td>
                                <td class="py-2.5 text-right text-slate-600 tabular-nums">{{ $currentWorkspace->formatMoney((int) $r->cost) }}</td>
                                <td class="py-2.5 text-right">
                                    @if($r->roas)
                                        <span class="rounded-md px-2 py-0.5 text-[11px] font-black {{ $r->roas >= 3 ? 'bg-emerald-100 text-emerald-700' : ($r->roas >= 1 ? 'bg-amber-100 text-amber-700' : 'bg-rose-100 text-rose-700') }}">
                                            {{ number_format((float) $r->roas, 1) }}×
                                        </span>
                                    @else
                                        <span class="text-slate-400">—</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    {{-- ============================ TOP CONTENT ============================ --}}
    @if($topContent->isNotEmpty())
        <div class="mt-5 rounded-2xl border border-slate-200 bg-white p-5">
            <div class="flex items-center justify-between">
                <h2 class="text-sm font-bold text-slate-900">Top-performing content</h2>
                <span class="text-[11px] text-slate-500">Approved submissions, most recent</span>
            </div>
            <div class="mt-4 grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                @foreach($topContent as $sub)
                    <div class="rounded-xl border border-slate-100 p-3">
                        <div class="flex items-center gap-2">
                            <span class="grid h-8 w-8 shrink-0 place-items-center rounded-lg bg-slate-900 text-white">
                                <x-icon name="video" class="h-4 w-4" />
                            </span>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-xs font-semibold text-slate-900">
                                    {{ $sub->assignment?->creator?->display_name ?? 'Creator' }}
                                </p>
                                <p class="truncate text-[11px] text-slate-500">
                                    {{ $sub->assignment?->campaign?->title ?? '—' }}
                                </p>
                            </div>
                            @if($sub->submitted_at)
                                <span class="text-[10px] text-slate-400">{{ $sub->submitted_at->diffForHumans(null, true) }}</span>
                            @endif
                        </div>
                        @if(! empty($sub->external_post_url))
                            <a href="{{ $sub->external_post_url }}" target="_blank" rel="noopener" class="mt-2 inline-flex items-center gap-1 text-[11px] font-semibold text-violet-700 hover:underline">
                                Open post <x-icon name="external" class="h-3 w-3" />
                            </a>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</x-layouts.app>
