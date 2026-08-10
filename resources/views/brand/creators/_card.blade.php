@props(['creator'])
<div class="card p-4">
    <div class="flex items-center gap-3">
        <div class="grid h-12 w-12 place-items-center rounded-full bg-rose-100 text-lg font-bold text-rose-700">{{ strtoupper(substr($creator->display_name,0,1)) }}</div>
        <div class="min-w-0 flex-1">
            <a href="{{ route('brand.creators.show', $creator) }}" class="block truncate font-semibold hover:underline">{{ $creator->display_name }}</a>
            <p class="truncate text-xs text-slate-500">{{ $creator->city ? $creator->city.', ' : '' }}{{ $creator->country }}</p>
        </div>
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

    <a href="{{ route('brand.creators.show', $creator) }}" class="btn-secondary mt-3 w-full !py-2 text-xs">View profile</a>
</div>
