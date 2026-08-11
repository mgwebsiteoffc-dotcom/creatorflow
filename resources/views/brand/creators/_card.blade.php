@props(['creator', 'activeCampaigns' => collect()])
@php
    $tier = $creator->currentTier();
    $tierMeta = $tier ? (\App\Support\CreatorTaxonomy::tiers()[$tier] ?? null) : null;
@endphp
<div class="card p-4 flex flex-col">
    <div class="flex items-center gap-3">
        <div class="grid h-12 w-12 place-items-center rounded-full bg-gradient-to-br from-violet-500 to-pink-500 text-lg font-bold text-white">{{ strtoupper(substr($creator->display_name,0,1)) }}</div>
        <div class="min-w-0 flex-1">
            <a href="{{ route('brand.creators.show', $creator) }}" class="block truncate font-semibold hover:underline">{{ $creator->display_name }}</a>
            <p class="truncate text-xs text-slate-500">{{ $creator->city ? $creator->city.', ' : '' }}{{ $creator->country ?: 'India' }}</p>
        </div>
        @if($tierMeta)
            <span class="rounded-full bg-amber-100 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider text-amber-700" title="{{ $tierMeta['range'] }}">{{ $tierMeta['label'] }}</span>
        @endif
    </div>

    <div class="mt-3 flex flex-wrap gap-1">
        @foreach($creator->nicheRows->take(3) as $n)
            <x-badge tone="violet">{{ $n->niche }}</x-badge>
        @endforeach
    </div>

    <dl class="mt-3 grid grid-cols-3 gap-2 text-center text-sm">
        <div><dt class="text-xs text-slate-500">Followers</dt><dd class="font-semibold">{{ number_format($creator->follower_count_total) }}</dd></div>
        <div><dt class="text-xs text-slate-500">Engage</dt><dd class="font-semibold">{{ $creator->engagement_rate }}%</dd></div>
        <div><dt class="text-xs text-slate-500">Score</dt><dd class="font-semibold text-emerald-600">{{ $creator->performance_score }}</dd></div>
    </dl>

    <div class="mt-auto pt-3 flex gap-2">
        <a href="{{ route('brand.creators.show', $creator) }}" class="btn-secondary flex-1 !py-2 text-xs">View profile</a>
        @if($activeCampaigns->isNotEmpty())
            <form method="POST" action="{{ route('brand.creators.invite') }}" class="flex-1">
                @csrf
                <input type="hidden" name="creator_id" value="{{ $creator->id }}">
                <div class="flex gap-1">
                    <select name="campaign_id" class="input !py-2 !px-2 text-xs flex-1 min-w-0" required>
                        @foreach($activeCampaigns as $c)
                            <option value="{{ $c->id }}">{{ \Illuminate\Support\Str::limit($c->title, 22) }}</option>
                        @endforeach
                    </select>
                    <button class="btn-primary !py-2 !px-3 text-xs" title="Invite to selected campaign">Invite</button>
                </div>
            </form>
        @endif
    </div>
</div>
