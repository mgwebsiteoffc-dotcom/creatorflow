<x-layouts.app :panel="'brand'" :title="$campaign->title">
    <a href="{{ route('brand.campaigns.index') }}" class="text-sm text-slate-500">← Campaigns</a>
    <div class="mt-2 flex flex-wrap items-start justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold">{{ $campaign->title }}</h1>
            <p class="text-sm text-slate-500">{{ $campaign->niche }} · <span class="capitalize">{{ $campaign->type }}</span></p>
        </div>
        @if(! $campaign->isLaunched())
            <form method="POST" action="{{ route('brand.campaigns.launch', $campaign) }}">
                @csrf
                <button class="btn-primary">🚀 Launch campaign</button>
            </form>
        @else
            <x-badge tone="green">Launched {{ $campaign->launched_at?->diffForHumans() }}</x-badge>
        @endif
    </div>

    <div class="mt-5 grid grid-cols-2 gap-3 md:grid-cols-5">
        <x-stat label="Invited" :value="$funnel['invited']" tone="slate"/>
        <x-stat label="Accepted" :value="$funnel['accepted']" tone="violet"/>
        <x-stat label="Shipped" :value="$funnel['shipped']" tone="sky"/>
        <x-stat label="Content" :value="$funnel['content']" tone="amber"/>
        <x-stat label="Approved" :value="$funnel['approved']" tone="emerald"/>
    </div>

    <div class="mt-6 grid gap-5 lg:grid-cols-3">
        <div class="card p-5 lg:col-span-2">
            <h2 class="font-semibold">Brief</h2>
            <div class="prose prose-sm mt-2 max-w-none whitespace-pre-wrap text-slate-700">{{ $campaign->brief ?: 'No brief yet.' }}</div>

            <h2 class="mt-6 font-semibold">Products</h2>
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

    <h2 class="mt-8 font-semibold">Assignments</h2>
    <div class="mt-3 space-y-2">
        @forelse($campaign->assignments as $a)
            <a href="{{ route('brand.assignments.show', $a) }}" class="card flex items-center gap-3 p-3">
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
