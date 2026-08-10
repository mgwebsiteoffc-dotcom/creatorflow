<x-layouts.app panel="creator" title="My profile">
    <div class="flex items-start justify-between gap-3">
        <h1 class="text-2xl font-bold">My profile</h1>
        <a href="{{ route('creator.profile.edit') }}" class="btn-secondary text-sm">Edit</a>
    </div>

    <div class="mt-4 grid gap-5 lg:grid-cols-3">
        <div class="card p-5 text-center lg:col-span-1">
            <div class="mx-auto grid h-24 w-24 place-items-center rounded-full bg-rose-100 text-3xl font-bold text-rose-700">{{ strtoupper(substr($creator->display_name,0,2)) }}</div>
            <h2 class="mt-3 text-lg font-bold">{{ $creator->display_name }}</h2>
            <p class="text-sm text-slate-500">{{ $creator->city ? $creator->city.', ' : '' }}{{ $creator->country }}</p>
            <div class="mt-3 flex flex-wrap justify-center gap-1">
                @foreach($creator->nicheRows as $n)<x-badge tone="violet">{{ $n->niche }}</x-badge>@endforeach
            </div>
            <p class="mt-4 text-sm text-slate-600">{{ $creator->bio }}</p>

            <dl class="mt-5 grid grid-cols-3 gap-2 text-center text-sm">
                <div><p class="text-slate-500">Followers</p><p class="font-semibold">{{ number_format($creator->follower_count_total) }}</p></div>
                <div><p class="text-slate-500">Engage</p><p class="font-semibold">{{ $creator->engagement_rate }}%</p></div>
                <div><p class="text-slate-500">Score</p><p class="font-semibold">{{ $creator->performance_score }}</p></div>
            </dl>
        </div>

        <div class="space-y-5 lg:col-span-2">
            <div class="card p-5">
                <h3 class="font-semibold">Rates &amp; preferences</h3>
                <div class="mt-3 grid grid-cols-2 gap-3 text-sm sm:grid-cols-4">
                    <div><p class="text-slate-500">UGC</p><p class="font-semibold">${{ number_format(($creator->rate_ugc_cents ?? 0)/100) }}</p></div>
                    <div><p class="text-slate-500">Video</p><p class="font-semibold">${{ number_format(($creator->rate_video_cents ?? 0)/100) }}</p></div>
                    <div><p class="text-slate-500">Post</p><p class="font-semibold">${{ number_format(($creator->rate_post_cents ?? 0)/100) }}</p></div>
                    <div><p class="text-slate-500">Story</p><p class="font-semibold">${{ number_format(($creator->rate_story_cents ?? 0)/100) }}</p></div>
                </div>
                <div class="mt-3 flex gap-4 text-sm">
                    <span class="{{ $creator->accepts_barter ? 'text-emerald-600' : 'text-slate-400' }}">● Barter</span>
                    <span class="{{ $creator->accepts_paid ? 'text-emerald-600' : 'text-slate-400' }}">● Paid</span>
                    <span class="{{ $creator->open_to_work ? 'text-emerald-600' : 'text-slate-400' }}">● Open to work</span>
                </div>
            </div>

            <div class="card p-5">
                <h3 class="font-semibold">Connected accounts</h3>
                <div class="mt-3 space-y-2">
                    @foreach($creator->socialAccounts as $s)
                        <div class="flex items-center justify-between rounded-xl border border-slate-100 p-3 text-sm">
                            <span class="capitalize">{{ $s->platform }} · {{ $s->handle }}</span>
                            <span class="text-slate-500">{{ number_format($s->follower_count) }} · {{ $s->engagement_rate }}%</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
