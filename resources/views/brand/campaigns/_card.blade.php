@props(['campaign'])
<a href="{{ route('brand.campaigns.show', $campaign) }}" class="card flex items-center gap-4 p-4 hover:border-violet-300">
    <div class="grid h-12 w-12 shrink-0 place-items-center rounded-xl bg-violet-100 text-lg">
        {{ match($campaign->type) { 'barter' => '🎁', 'paid' => '💸', 'affiliate' => '🔗', default => '🚀' } }}
    </div>
    <div class="min-w-0 flex-1">
        <div class="flex items-center gap-2">
            <p class="truncate font-semibold">{{ $campaign->title }}</p>
            @php
                $tones = ['draft' => 'slate','matching' => 'sky','inviting' => 'amber','active' => 'green','paused' => 'slate','completed' => 'violet','cancelled' => 'rose'];
            @endphp
            <x-badge :tone="$tones[$campaign->status] ?? 'slate'">{{ ucfirst($campaign->status) }}</x-badge>
        </div>
        <p class="mt-0.5 truncate text-xs text-slate-500">
            {{ $campaign->products_count ?? $campaign->products->count() }} products ·
            {{ $campaign->assignments_count ?? $campaign->assignments->count() }} creators ·
            target {{ $campaign->target_creators }}
        </p>
    </div>
    <svg class="h-5 w-5 text-slate-300" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
</a>
