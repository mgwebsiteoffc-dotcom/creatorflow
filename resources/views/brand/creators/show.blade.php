<x-layouts.app panel="brand" :title="$creator->display_name">
    <a href="{{ route('brand.creators.index') }}" class="text-sm text-slate-500">← Marketplace</a>
    <div class="mt-2 grid gap-5 md:grid-cols-3">
        <div class="card p-5 md:col-span-1">
            <div class="grid h-20 w-20 place-items-center rounded-full bg-rose-100 text-2xl font-bold text-rose-700">{{ strtoupper(substr($creator->display_name,0,2)) }}</div>
            <h1 class="mt-3 text-xl font-bold">{{ $creator->display_name }}</h1>
            <p class="text-sm text-slate-500">{{ $creator->city ? $creator->city.', ' : '' }}{{ $creator->country }}</p>
            <p class="mt-3 text-sm text-slate-700">{{ $creator->bio }}</p>

            <div class="mt-4 flex flex-wrap gap-1">
                @foreach($creator->nicheRows as $n)<x-badge tone="violet">{{ $n->niche }}</x-badge>@endforeach
            </div>

            <dl class="mt-5 grid grid-cols-2 gap-3 text-sm">
                <div><p class="text-slate-500">Followers</p><p class="font-semibold">{{ number_format($creator->follower_count_total) }}</p></div>
                <div><p class="text-slate-500">Engagement</p><p class="font-semibold">{{ $creator->engagement_rate }}%</p></div>
                <div><p class="text-slate-500">Perf. score</p><p class="font-semibold">{{ $creator->performance_score }}</p></div>
                <div><p class="text-slate-500">Fraud risk</p><p class="font-semibold">{{ $creator->fraud_risk }}%</p></div>
            </dl>
        </div>

        <div class="space-y-5 md:col-span-2">
            <div class="card p-5">
                <h2 class="font-semibold">Social accounts</h2>
                <div class="mt-3 grid gap-2 sm:grid-cols-2">
                    @foreach($creator->socialAccounts as $s)
                        <div class="rounded-xl border border-slate-100 p-3">
                            <p class="text-sm font-medium capitalize">{{ $s->platform }} · {{ '@'.$s->handle }}</p>
                            <p class="text-xs text-slate-500">{{ number_format($s->follower_count) }} followers · {{ $s->engagement_rate }}%</p>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="card p-5">
                <h2 class="font-semibold">Invite to campaign</h2>
                @php $camps = auth()->user()->workspaces->flatMap->campaigns()->whereIn('status', ['draft','inviting','active'])->get(); @endphp
                @if($camps->isEmpty())
                    <p class="mt-2 text-sm text-slate-500">Create an active campaign first.</p>
                @else
                    <form method="POST" action="{{ route('brand.creators.invite') }}" class="mt-3 space-y-3">
                        @csrf
                        <input type="hidden" name="creator_id" value="{{ $creator->id }}">
                        <select name="campaign_id" class="input">
                            @foreach($camps as $c)<option value="{{ $c->id }}">{{ $c->title }}</option>@endforeach
                        </select>
                        <textarea name="message" class="input min-h-20" placeholder="Personalized invite message…"></textarea>
                        <button class="btn-primary">Send invite</button>
                    </form>
                @endif
            </div>
        </div>
    </div>
</x-layouts.app>
