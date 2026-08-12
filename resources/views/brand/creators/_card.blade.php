@props(['creator', 'activeCampaigns' => collect()])
@php
    $tier      = $creator->currentTier();
    $tierMeta  = $tier ? (\App\Support\CreatorTaxonomy::tiers()[$tier] ?? null) : null;
    $niches    = $creator->nicheRows->pluck('niche')->take(3)->all();
    $extra     = max(0, $creator->nicheRows->count() - 3);
    $location  = trim(($creator->city ?: '').($creator->country ? ', '.$creator->country : ''), ', ') ?: '—';

    // Compact follower formatting: 165,542 → "165K", 1,240,000 → "1.2M"
    $fmtCompact = function (int $n): string {
        if ($n >= 1_000_000) return rtrim(rtrim(number_format($n / 1_000_000, 1), '0'), '.').'M';
        if ($n >= 1_000)     return rtrim(rtrim(number_format($n / 1_000, 1),     '0'), '.').'K';
        return (string) $n;
    };
@endphp

<article class="relative flex flex-col rounded-xl border border-slate-200 bg-white p-4 transition hover:-translate-y-0.5 hover:border-violet-300 hover:shadow-md">

    {{-- Header: avatar · name · location · tier badge --}}
    <div class="flex items-start gap-3">
        <div class="grid h-10 w-10 shrink-0 place-items-center rounded-full bg-gradient-to-br from-violet-500 to-pink-500 text-sm font-black text-white">
            {{ strtoupper(substr($creator->display_name, 0, 1)) }}
        </div>
        <div class="min-w-0 flex-1">
            <a href="{{ route('brand.creators.show', $creator) }}" class="block truncate text-sm font-bold text-slate-900 hover:text-violet-700">
                {{ $creator->display_name }}
            </a>
            <p class="mt-0.5 flex items-center gap-1 truncate text-[11px] text-slate-500">
                <x-icon name="map-pin" class="h-3 w-3 text-slate-400" />
                <span class="truncate">{{ $location }}</span>
            </p>
        </div>
        @if($tierMeta)
            <span class="shrink-0 rounded-md bg-amber-100 px-1.5 py-0.5 text-[9px] font-black uppercase tracking-wider text-amber-700" title="{{ $tierMeta['range'] }}">
                {{ $tierMeta['label'] }}
            </span>
        @endif
    </div>

    {{-- Niche pills --}}
    @if(count($niches))
        <div class="mt-2 flex flex-wrap gap-1">
            @foreach($niches as $n)
                <span class="rounded-md bg-slate-100 px-1.5 py-0.5 text-[10px] font-semibold text-slate-600">{{ $n }}</span>
            @endforeach
            @if($extra > 0)
                <span class="rounded-md bg-slate-50 px-1.5 py-0.5 text-[10px] font-semibold text-slate-500">+{{ $extra }}</span>
            @endif
        </div>
    @endif

    {{-- Stats strip: 3 columns · big numbers, tiny labels --}}
    <dl class="mt-3 grid grid-cols-3 divide-x divide-slate-100 rounded-lg border border-slate-100 bg-slate-50/60">
        <div class="px-2 py-2 text-center">
            <dt class="text-[9px] font-bold uppercase tracking-wider text-slate-400">Followers</dt>
            <dd class="mt-0.5 text-sm font-black text-slate-900 tabular-nums">{{ $fmtCompact((int) $creator->follower_count_total) }}</dd>
        </div>
        <div class="px-2 py-2 text-center">
            <dt class="text-[9px] font-bold uppercase tracking-wider text-slate-400">Engage</dt>
            <dd class="mt-0.5 text-sm font-black text-slate-900 tabular-nums">{{ number_format((float) $creator->engagement_rate, 1) }}%</dd>
        </div>
        <div class="px-2 py-2 text-center">
            <dt class="text-[9px] font-bold uppercase tracking-wider text-slate-400">Score</dt>
            <dd class="mt-0.5 text-sm font-black text-emerald-600 tabular-nums">{{ (int) $creator->performance_score }}</dd>
        </div>
    </dl>

    {{-- Deal-type badges (only when set) --}}
    <div class="mt-2 flex flex-wrap gap-1 text-[10px]">
        @if($creator->accepts_barter)
            <span class="inline-flex items-center gap-1 rounded-md bg-emerald-50 px-1.5 py-0.5 font-semibold text-emerald-700">
                <x-icon name="gift" class="h-2.5 w-2.5" /> Barter
            </span>
        @endif
        @if($creator->accepts_paid)
            <span class="inline-flex items-center gap-1 rounded-md bg-violet-50 px-1.5 py-0.5 font-semibold text-violet-700">
                <x-icon name="rupee" class="h-2.5 w-2.5" /> Paid
            </span>
        @endif
    </div>

    {{-- Actions row: view + invite --}}
    <div class="mt-auto pt-3 flex items-stretch gap-1.5">
        <a href="{{ route('brand.creators.show', $creator) }}"
           class="btn-secondary flex-1 !py-1.5 !text-xs">View profile</a>

        @if($activeCampaigns->isNotEmpty())
            <form method="POST" action="{{ route('brand.creators.invite') }}" class="flex-1">
                @csrf
                <input type="hidden" name="creator_id" value="{{ $creator->id }}">
                <div class="flex items-stretch gap-1">
                    <select name="campaign_id"
                            class="input !py-1 !px-1.5 !text-xs min-w-0 flex-1"
                            required
                            aria-label="Campaign to invite to">
                        @foreach($activeCampaigns as $c)
                            <option value="{{ $c->id }}">{{ \Illuminate\Support\Str::limit($c->title, 18) }}</option>
                        @endforeach
                    </select>
                    <button class="btn-primary !py-1 !px-2 !text-xs" title="Invite to selected campaign">
                        Invite
                    </button>
                </div>
            </form>
        @endif
    </div>
</article>
