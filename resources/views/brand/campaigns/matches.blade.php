<x-layouts.app panel="brand" title="Creator matches">
    <a href="{{ route('brand.campaigns.show', $campaign) }}" class="text-sm text-slate-500">← {{ $campaign->title }}</a>
    <h1 class="mt-1 text-2xl font-bold">Creator matches</h1>
    <p class="text-sm text-slate-500">{{ $matches->total() }} candidates ranked by AI.</p>

    <div class="mt-5 space-y-3">
        @foreach($matches as $match)
            @php $c = $match->creator; @endphp
            <div class="card flex items-center gap-4 p-4">
                <a href="{{ route('brand.creators.show', $c) }}" class="grid h-12 w-12 shrink-0 place-items-center rounded-full bg-rose-100 font-bold text-rose-700">{{ strtoupper(substr($c->display_name,0,1)) }}</a>
                <div class="min-w-0 flex-1">
                    <a href="{{ route('brand.creators.show', $c) }}" class="block truncate font-semibold hover:underline">{{ $c->display_name }}</a>
                    <p class="truncate text-xs text-slate-500">
                        {{ number_format($c->follower_count_total) }} followers · {{ $c->engagement_rate }}% engagement ·
                        {{ $c->nicheRows->pluck('niche')->implode(', ') }}
                    </p>
                    <div class="mt-1 flex flex-wrap gap-1">
                        @foreach(($match->reasons ?? []) as $reason)
                            <span class="badge-slate">{{ $reason }}</span>
                        @endforeach
                    </div>
                </div>
                <div class="text-right">
                    <div class="text-2xl font-bold text-violet-600">{{ round($match->score) }}</div>
                    <div class="text-xs text-slate-500">match</div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="mt-6">{{ $matches->links() }}</div>
</x-layouts.app>
