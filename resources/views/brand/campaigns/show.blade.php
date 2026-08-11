<x-layouts.app :panel="'brand'" :title="$campaign->title">
    <a href="{{ route('brand.campaigns.index') }}" class="text-sm text-slate-500 hover:text-slate-800">← Campaigns</a>
    <div class="mt-2 flex flex-wrap items-start justify-between gap-3">
        <div>
            <h1 class="text-3xl font-black tracking-tight text-slate-900">{{ $campaign->title }}</h1>
            <p class="mt-1 text-sm text-slate-500">{{ $campaign->niche }} · <span class="capitalize">{{ $campaign->type }}</span></p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            @if(! $campaign->isLaunched())
                <form method="POST" action="{{ route('brand.campaigns.launch', $campaign) }}">
                    @csrf
                    <button class="btn-primary">🚀 Launch campaign</button>
                </form>
            @else
                <x-badge tone="green">Launched {{ $campaign->launched_at?->diffForHumans() }}</x-badge>
            @endif

            @if(in_array($campaign->status, ['inviting','active']))
                <form method="POST" action="{{ route('brand.campaigns.pause', $campaign) }}">@csrf
                    <button class="btn-secondary !py-2 !text-xs">⏸ Pause</button>
                </form>
            @endif
            @if($campaign->status === 'paused')
                <form method="POST" action="{{ route('brand.campaigns.resume', $campaign) }}">@csrf
                    <button class="btn-secondary !py-2 !text-xs">▶ Resume</button>
                </form>
            @endif
            @if(in_array($campaign->status, ['inviting','active','paused']))
                <form method="POST" action="{{ route('brand.campaigns.end', $campaign) }}" data-confirm="Mark this campaign as completed?">@csrf
                    <button class="btn-secondary !py-2 !text-xs">🏁 End campaign</button>
                </form>
            @endif
            @if($campaign->status !== 'cancelled' && $campaign->status !== 'completed')
                <form method="POST" action="{{ route('brand.campaigns.cancel', $campaign) }}"
                      data-confirm="Cancel this campaign? Pending invitations will be expired.">@csrf
                    <button class="btn-ghost !py-2 !text-xs !text-rose-600 hover:!bg-rose-50">✕ Cancel</button>
                </form>
            @endif
        </div>
    </div>

    <div class="mt-6 grid grid-cols-2 gap-4 md:grid-cols-5 lg:gap-5">
        <x-stat label="Invited" :value="$funnel['invited']" tone="slate"/>
        <x-stat label="Accepted" :value="$funnel['accepted']" tone="violet"/>
        <x-stat label="Shipped" :value="$funnel['shipped']" tone="sky"/>
        <x-stat label="Content" :value="$funnel['content']" tone="amber"/>
        <x-stat label="Approved" :value="$funnel['approved']" tone="emerald"/>
    </div>

    {{-- CAMPAIGN-WIDE STATS + ROAS --}}
    <div class="mt-6 grid gap-4 md:grid-cols-3">
        <div class="rounded-2xl border border-slate-200 bg-white p-5">
            <div class="text-[11px] font-bold uppercase tracking-widest text-slate-500">Works completed</div>
            <div class="mt-1 text-3xl font-black text-slate-900">{{ $campaignStats['completed_works'] }} <span class="text-base font-semibold text-slate-400">/ {{ $campaign->target_creators }}</span></div>
            <div class="mt-3 h-2 rounded-full bg-slate-100">
                <div class="h-2 rounded-full" style="width: {{ $campaign->target_creators > 0 ? min(100, ($campaignStats['completed_works'] / max($campaign->target_creators, 1)) * 100) : 0 }}%; background-image: linear-gradient(90deg,#10b981,#22d3ee);"></div>
            </div>
            <div class="mt-3 flex flex-wrap gap-x-4 gap-y-1 text-xs text-slate-500">
                <span>🎬 {{ $campaignStats['in_progress'] }} in progress</span>
                <span>👀 {{ $campaignStats['needs_review'] }} needs review</span>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-5">
            <div class="text-[11px] font-bold uppercase tracking-widest text-slate-500">Applications</div>
            <div class="mt-1 text-3xl font-black text-slate-900">{{ $campaignStats['applications_count'] }}</div>
            <div class="mt-3 flex flex-wrap gap-x-4 gap-y-1 text-xs text-slate-500">
                <span class="text-emerald-600">✓ {{ $campaignStats['approved_applications'] }} approved</span>
                <span class="text-rose-600">✕ {{ $campaignStats['rejected_applications'] }} rejected</span>
                @if($campaignStats['days_running'] > 0)
                    <span>📅 {{ $campaignStats['days_running'] }} days running</span>
                @endif
            </div>
        </div>

        <div class="rounded-2xl bg-gradient-to-br from-emerald-500 to-teal-500 p-5 text-white shadow-md">
            <div class="text-[11px] font-bold uppercase tracking-widest opacity-90">Attributed revenue</div>
            <div class="mt-1 text-3xl font-black">{{ $currentWorkspace->formatMoney($campaignStats['attributed_revenue']) }}</div>
            <div class="mt-3 flex flex-wrap gap-x-4 gap-y-1 text-xs opacity-90">
                <span>🛒 {{ $campaignStats['attributed_orders'] }} orders</span>
                <span>💸 spent {{ $currentWorkspace->formatMoney($campaignStats['total_cost']) }}</span>
                @if($campaignStats['roas'])
                    <span>📈 ROAS {{ $campaignStats['roas'] }}×</span>
                @endif
            </div>
        </div>
    </div>

    {{-- PENDING APPLICATIONS --}}
    @if($pendingApplications->isNotEmpty())
        <section class="mt-8 g-border p-1">
            <div class="rounded-[calc(1.25rem-1px)] bg-white p-5 md:p-6">
                <div class="flex items-center justify-between gap-3">
                    <div>
                        <h2 class="text-lg font-bold text-slate-900">
                            {{ $pendingApplications->count() }} pending {{ Str::plural('application', $pendingApplications->count()) }}
                        </h2>
                        <p class="text-sm text-slate-500">Review and approve creators who applied to this campaign.</p>
                    </div>
                    <a href="{{ route('brand.applications.index') }}" class="btn-ghost !py-1.5 text-xs">All applications →</a>
                </div>

                <div class="mt-4 space-y-3">
                    @foreach($pendingApplications->take(5) as $app)
                        @include('brand.applications._row', ['app' => $app])
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <div class="mt-8 grid gap-5 lg:grid-cols-3">
        <div class="card p-5 lg:col-span-2">
            <div class="flex items-center gap-2">
                <div class="grid h-9 w-9 place-items-center rounded-xl bg-gradient-to-br from-violet-500 to-pink-500 text-white">📋</div>
                <h2 class="text-lg font-bold text-slate-900">Brief</h2>
            </div>
            <div class="mt-4">
                <x-brief :markdown="$campaign->brief" />
            </div>

            <h2 class="mt-8 text-lg font-bold text-slate-900">Products</h2>
            <div class="mt-2 space-y-2">
                @foreach($campaign->products as $cp)
                    <div class="flex items-center gap-3 rounded-xl border border-slate-100 p-3">
                        <div class="h-10 w-10 rounded-lg bg-slate-100">
                            @if($cp->product->primaryImage)
                                <img src="{{ $cp->product->primaryImage->path }}" class="h-10 w-10 rounded-lg object-cover">
                            @endif
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-medium">{{ $cp->product->title }}</p>
                            <p class="text-xs text-slate-500">{{ $cp->accepted_count }}/{{ $cp->target_creators }} accepted · {{ $cp->content_received_count }} content</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="card p-5">
            <div class="flex items-center justify-between">
                <h2 class="font-semibold">Top matches</h2>
                <a href="{{ route('brand.campaigns.matches', $campaign) }}" class="text-sm font-medium text-violet-600">View all</a>
            </div>
            <div class="mt-3 space-y-2">
                @forelse($campaign->matches->sortByDesc('score')->take(5) as $match)
                    <div class="flex items-center gap-3">
                        <div class="grid h-9 w-9 place-items-center rounded-full bg-rose-100 font-bold text-rose-700">{{ strtoupper(substr($match->creator->display_name,0,1)) }}</div>
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-medium">{{ $match->creator->display_name }}</p>
                            <p class="truncate text-xs text-slate-500">{{ $match->creator->follower_count_total }} followers</p>
                        </div>
                        <x-badge tone="violet">{{ round($match->score) }}</x-badge>
                    </div>
                @empty
                    <p class="text-sm text-slate-500">Matches generate when you launch.</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- REFERENCES MANAGER --}}
    <div class="mt-6">
        @include('partials.references-manager', ['campaign' => $campaign])
    </div>

    {{-- ACTIVITY TIMELINE --}}
    <section class="mt-10 rounded-2xl border border-slate-200 bg-white p-5 md:p-6">
        <div class="flex items-center gap-2">
            <div class="grid h-9 w-9 place-items-center rounded-xl bg-gradient-to-br from-slate-800 to-slate-950 text-white">⏱</div>
            <h2 class="text-lg font-bold text-slate-900">Activity timeline</h2>
            <span class="ml-auto text-xs text-slate-500">{{ $timeline->count() }} events</span>
        </div>

        <ol class="mt-6 relative border-s border-slate-200 ps-6">
            @forelse($timeline as $ev)
                <li class="mb-6 last:mb-0">
                    <span class="absolute -start-3 grid h-6 w-6 place-items-center rounded-full border-2 border-white bg-gradient-to-br from-violet-500 to-pink-500 text-[11px] text-white shadow-sm">{{ $ev['icon'] }}</span>
                    <div class="flex flex-wrap items-baseline justify-between gap-2">
                        <div class="text-sm font-semibold text-slate-900">{{ $ev['title'] }}</div>
                        <time class="text-[11px] text-slate-400">{{ $ev['at']?->diffForHumans() ?? '' }}</time>
                    </div>
                    <div class="mt-0.5 text-xs text-slate-500">{{ $ev['body'] }}</div>
                </li>
            @empty
                <li class="text-sm text-slate-500">No activity yet.</li>
            @endforelse
        </ol>
    </section>

    <h2 class="mt-10 text-lg font-bold">Assignments</h2>
    <div class="mt-4 space-y-2">
        @forelse($campaign->assignments as $a)
            <a href="{{ route('brand.assignments.show', $a) }}" class="card flex items-center gap-3 p-4 hover:border-violet-300">
                <div class="grid h-10 w-10 place-items-center rounded-full bg-rose-100 font-bold text-rose-700">{{ strtoupper(substr($a->creator->display_name,0,1)) }}</div>
                <div class="min-w-0 flex-1">
                    <p class="truncate text-sm font-medium">{{ $a->creator->display_name }}</p>
                    <p class="text-xs text-slate-500">{{ $a->campaignProduct->product->title ?? '' }}</p>
                </div>
                <x-badge :tone="in_array($a->status,['completed','approved']) ? 'green' : (in_array($a->status,['submitted','changes_requested']) ? 'amber' : 'slate')">{{ str_replace('_',' ', $a->status) }}</x-badge>
            </a>
        @empty
            <p class="text-sm text-slate-500">No assignments yet. Launch to start inviting creators.</p>
        @endforelse
    </div>
</x-layouts.app>
