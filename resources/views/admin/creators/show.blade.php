<x-layouts.admin :title="$creator->display_name">
    <a href="{{ route('admin.creators.index') }}" class="text-sm text-slate-500 hover:text-slate-800">← Creators</a>

    <div class="mt-3 overflow-hidden rounded-3xl border border-slate-200 bg-white">
        <div class="relative h-24" style="background-image: linear-gradient(120deg,#f43f5e 0%,#ec4899 50%,#f59e0b 110%);"></div>
        <div class="relative px-6 pb-6 pt-0">
            <div class="-mt-10 flex flex-wrap items-end justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="grid h-20 w-20 shrink-0 place-items-center rounded-2xl bg-white text-2xl font-black text-slate-800 shadow-lg ring-4 ring-white">
                        {{ strtoupper(substr($creator->display_name, 0, 2)) }}
                    </div>
                    <div class="pt-8">
                        <h1 class="text-2xl font-black tracking-tight text-slate-900">{{ $creator->display_name }}</h1>
                        <p class="text-sm text-slate-500">
                            {{ $creator->email ?: 'no email' }}
                            @if($creator->city || $creator->country) · 📍 {{ trim($creator->city.', '.$creator->country, ', ') }} @endif
                        </p>
                    </div>
                </div>
                <div class="flex flex-wrap gap-2 pt-8">
                    <x-badge :tone="$creator->status === 'active' ? 'green' : ($creator->status === 'suspended' ? 'rose' : 'amber')">{{ $creator->status }}</x-badge>
                    @if($creator->open_to_work)<x-badge tone="sky">Open to work</x-badge>@endif
                </div>
            </div>

            <div class="mt-6 grid gap-3 md:grid-cols-4">
                <div class="rounded-2xl border border-slate-100 p-3">
                    <div class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Total followers</div>
                    <div class="mt-1 text-2xl font-black text-slate-900">{{ number_format($creator->follower_count_total) }}</div>
                </div>
                <div class="rounded-2xl border border-slate-100 p-3">
                    <div class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Engagement</div>
                    <div class="mt-1 text-2xl font-black text-slate-900">{{ $creator->engagement_rate }}%</div>
                </div>
                <div class="rounded-2xl border border-slate-100 p-3">
                    <div class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Performance score</div>
                    <div class="mt-1 text-2xl font-black text-slate-900">{{ (int) $creator->performance_score }}</div>
                </div>
                <div class="rounded-2xl border border-slate-100 p-3">
                    <div class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Fraud risk</div>
                    <div class="mt-1 text-2xl font-black text-slate-900">{{ (int) $creator->fraud_risk }}%</div>
                </div>
            </div>
        </div>
    </div>

    {{-- ACTIONS + BIO --}}
    <div class="mt-8 grid gap-6 lg:grid-cols-3">
        <section class="lg:col-span-2 rounded-2xl border border-slate-200 bg-white p-6">
            <h2 class="text-lg font-bold text-slate-900">About</h2>
            <p class="mt-2 text-sm text-slate-600">{{ $creator->bio ?: 'No bio provided.' }}</p>

            @if($creator->nicheRows->isNotEmpty())
                <div class="mt-4 flex flex-wrap gap-1">
                    @foreach($creator->nicheRows as $n)<x-badge tone="violet">{{ $n->niche }}</x-badge>@endforeach
                </div>
            @endif

            @if($creator->socialAccounts->isNotEmpty())
                <h3 class="mt-6 text-sm font-bold text-slate-800">Social accounts</h3>
                <div class="mt-2 grid gap-2 sm:grid-cols-2">
                    @foreach($creator->socialAccounts as $s)
                        <div class="rounded-xl border border-slate-100 p-3">
                            <div class="text-sm font-semibold capitalize text-slate-900">{{ $s->platform }} · {{ '@'.$s->handle }}</div>
                            <div class="text-xs text-slate-500">{{ number_format($s->follower_count) }} followers · {{ $s->engagement_rate }}% ER</div>
                        </div>
                    @endforeach
                </div>
            @endif

            @if($creator->user)
                <a href="{{ route('admin.users.show', $creator->user) }}" class="mt-6 inline-block text-sm font-semibold text-violet-700 hover:text-violet-900">Linked user account →</a>
            @endif
        </section>

        <section class="rounded-2xl border border-slate-200 bg-white p-6">
            <h2 class="text-lg font-bold text-slate-900">Actions</h2>
            <div class="mt-4 flex flex-col gap-2">
                @if($creator->status === 'suspended')
                    <form method="POST" action="{{ route('admin.creators.reinstate', $creator) }}">@csrf
                        <button class="btn-primary w-full !py-1.5 text-xs">Reinstate creator</button>
                    </form>
                @else
                    <form method="POST" action="{{ route('admin.creators.suspend', $creator) }}" data-confirm="Ban {{ $creator->display_name }}?">@csrf
                        <input type="hidden" name="reason" value="Banned by admin">
                        <button class="btn-ghost w-full !py-1.5 text-xs !text-rose-600 hover:!bg-rose-50">Ban creator</button>
                    </form>
                @endif
            </div>

            <h3 class="mt-6 text-sm font-bold text-slate-800">Earnings</h3>
            <div class="mt-2 space-y-2 text-sm">
                <div class="flex justify-between rounded-xl bg-emerald-50 p-3">
                    <span class="text-emerald-700">Paid</span>
                    <span class="font-black text-emerald-800">${{ number_format($stats['earnings_paid']/100, 2) }}</span>
                </div>
                <div class="flex justify-between rounded-xl bg-amber-50 p-3">
                    <span class="text-amber-700">Pending</span>
                    <span class="font-black text-amber-800">${{ number_format($stats['earnings_pending']/100, 2) }}</span>
                </div>
            </div>
        </section>
    </div>

    {{-- ASSIGNMENTS --}}
    <section class="mt-8 rounded-2xl border border-slate-200 bg-white p-6">
        <h2 class="text-lg font-bold text-slate-900">🎬 Recent assignments</h2>
        <div class="mt-4 overflow-x-auto rounded-xl border border-slate-100">
            <table class="w-full min-w-[640px] text-sm">
                <thead class="border-b border-slate-100 bg-slate-50 text-left text-xs uppercase tracking-widest text-slate-500">
                    <tr><th class="p-3">When</th><th class="p-3">Campaign</th><th class="p-3">Brand</th><th class="p-3">Product</th><th class="p-3 text-right">Status</th></tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($assignments as $a)
                        <tr>
                            <td class="p-3 text-xs text-slate-500">{{ $a->created_at->format('M j, Y') }}</td>
                            <td class="p-3 font-semibold text-slate-800">{{ $a->campaign->title ?? '—' }}</td>
                            <td class="p-3 text-slate-600">
                                @if($a->campaign?->workspace)
                                    <a href="{{ route('admin.workspaces.show', $a->campaign->workspace) }}" class="hover:text-violet-700">{{ $a->campaign->workspace->name }}</a>
                                @else — @endif
                            </td>
                            <td class="p-3 text-slate-600">{{ $a->campaignProduct?->product?->title ?? '—' }}</td>
                            <td class="p-3 text-right"><x-badge :tone="in_array($a->status,['approved','completed']) ? 'green' : (in_array($a->status,['submitted','changes_requested']) ? 'amber' : 'slate')">{{ str_replace('_',' ',$a->status) }}</x-badge></td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="p-6 text-center text-sm text-slate-500">No assignments yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    {{-- APPLICATIONS --}}
    @if($applications->isNotEmpty())
        <section class="mt-8 rounded-2xl border border-slate-200 bg-white p-6">
            <h2 class="text-lg font-bold text-slate-900">📝 Applications</h2>
            <div class="mt-4 space-y-2">
                @foreach($applications as $app)
                    <div class="flex items-center justify-between rounded-xl border border-slate-100 p-3">
                        <div class="min-w-0">
                            <div class="text-sm font-semibold text-slate-900">{{ $app->campaign->title ?? '—' }}</div>
                            <div class="text-xs text-slate-500">{{ $app->campaign->workspace->name ?? 'Brand' }} · {{ $app->created_at->diffForHumans() }}</div>
                        </div>
                        <x-badge :tone="match($app->status) { 'approved' => 'green', 'rejected' => 'rose', 'shortlisted' => 'sky', default => 'amber' }">{{ $app->status }}</x-badge>
                    </div>
                @endforeach
            </div>
        </section>
    @endif

    {{-- PAYOUTS --}}
    @if($payouts->isNotEmpty())
        <section class="mt-8 rounded-2xl border border-slate-200 bg-white p-6">
            <h2 class="text-lg font-bold text-slate-900">💰 Payout history</h2>
            <div class="mt-4 overflow-x-auto rounded-xl border border-slate-100">
                <table class="w-full min-w-[640px] text-sm">
                    <thead class="border-b border-slate-100 bg-slate-50 text-left text-xs uppercase tracking-widest text-slate-500">
                        <tr><th class="p-3">When</th><th class="p-3">Assignment</th><th class="p-3 text-right">Net</th><th class="p-3 text-right">Status</th></tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($payouts as $p)
                            <tr>
                                <td class="p-3 text-xs text-slate-500">{{ optional($p->paid_at ?: $p->created_at)->format('M j, Y') }}</td>
                                <td class="p-3 text-slate-800">#{{ $p->assignment_id }}</td>
                                <td class="p-3 text-right font-mono font-semibold text-slate-900">${{ number_format($p->net_cents/100, 2) }}</td>
                                <td class="p-3 text-right"><x-badge :tone="$p->status === 'paid' ? 'green' : ($p->status === 'pending' ? 'amber' : 'slate')">{{ $p->status }}</x-badge></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>
    @endif
</x-layouts.admin>
