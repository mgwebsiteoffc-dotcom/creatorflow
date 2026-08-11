@props(['app'])
@php
    $c = $app->creator;
    $badgeTone = match($app->status) {
        'submitted' => 'amber',
        'shortlisted' => 'sky',
        'approved' => 'green',
        'rejected' => 'rose',
        'withdrawn' => 'slate',
        default => 'slate',
    };
    $badgeLabel = ucfirst($app->status);
    $totalFollowers = $c ? $c->socialAccounts->sum('follower_count') : 0;
@endphp
<div class="card p-5">
    <div class="flex flex-wrap items-start gap-4">
        {{-- Avatar --}}
        <div class="grid h-14 w-14 shrink-0 place-items-center rounded-2xl bg-gradient-to-br from-violet-500 to-pink-500 text-lg font-bold text-white">
            {{ strtoupper(substr($c->display_name ?? 'C', 0, 1)) }}
        </div>

        {{-- Body --}}
        <div class="min-w-0 flex-1">
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('brand.creators.show', $c) }}" class="font-bold text-slate-900 hover:text-violet-700">{{ $c->display_name }}</a>
                <x-badge :tone="$badgeTone">{{ $badgeLabel }}</x-badge>
                <span class="text-xs text-slate-500">
                    applied to <a href="{{ route('brand.campaigns.show', $app->campaign) }}" class="font-medium text-slate-700 hover:text-violet-700">{{ $app->campaign->title }}</a>
                    · {{ $app->created_at->diffForHumans() }}
                </span>
            </div>

            <div class="mt-2 flex flex-wrap gap-x-4 gap-y-1 text-xs text-slate-500">
                @if($c->country)<span>📍 {{ $c->country }}</span>@endif
                @if($totalFollowers > 0)<span>👥 {{ number_format($totalFollowers) }} followers</span>@endif
                @if($c->engagement_rate)<span>💥 {{ $c->engagement_rate }}% ER</span>@endif
                @if($app->proposed_fee_cents)<span>💰 asks ${{ number_format($app->proposed_fee_cents/100, 0) }}</span>@endif
            </div>

            @if($c->nicheRows->isNotEmpty())
                <div class="mt-2 flex flex-wrap gap-1">
                    @foreach($c->nicheRows->take(4) as $n)<x-badge tone="violet">{{ $n->niche }}</x-badge>@endforeach
                </div>
            @endif

            @if($app->cover_note)
                <p class="mt-3 rounded-xl bg-slate-50 p-3 text-sm text-slate-700">
                    <span class="text-xs font-bold uppercase tracking-widest text-slate-400">Cover note</span><br>
                    {{ $app->cover_note }}
                </p>
            @endif
        </div>

        {{-- Actions --}}
        <div class="flex shrink-0 flex-col gap-2 md:min-w-[10rem]">
            @if(in_array($app->status, ['submitted','shortlisted']))
                <form method="POST" action="{{ route('brand.applications.approve', $app) }}">
                    @csrf
                    <button class="btn-primary w-full !py-1.5 text-xs">✓ Approve</button>
                </form>
                @if($app->status !== 'shortlisted')
                    <form method="POST" action="{{ route('brand.applications.shortlist', $app) }}">
                        @csrf
                        <button class="btn-secondary w-full !py-1.5 text-xs">★ Shortlist</button>
                    </form>
                @endif
                <form method="POST" action="{{ route('brand.applications.reject', $app) }}"
                      data-confirm="Reject this application?">
                    @csrf
                    <button class="btn-ghost w-full !py-1.5 text-xs !text-rose-600 hover:!bg-rose-50">Reject</button>
                </form>
            @elseif($app->status === 'approved')
                <div class="rounded-xl bg-emerald-50 p-3 text-center text-xs font-semibold text-emerald-700">
                    ✓ Invited creator
                </div>
            @endif
            <a href="{{ route('brand.creators.show', $c) }}" class="btn-ghost w-full !py-1.5 text-xs">View profile</a>
        </div>
    </div>
</div>
