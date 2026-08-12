@props(['campaign'])
@php
    $tones = ['draft' => 'slate','matching' => 'sky','inviting' => 'amber','active' => 'green','paused' => 'slate','completed' => 'violet','cancelled' => 'rose'];
    $grad = match($campaign->type) {
        'barter' => 'from-violet-500 to-pink-500',
        'paid' => 'from-emerald-500 to-teal-500',
        'affiliate' => 'from-cyan-500 to-blue-500',
        default => 'from-slate-600 to-slate-800',
    };
    $icon = match($campaign->type) { 'barter' => '', 'paid' => '', 'affiliate' => '', default => '' };
@endphp
<a href="{{ route('brand.campaigns.show', $campaign) }}"
   class="card flex items-center gap-4 p-5 transition hover:-translate-y-0.5 hover:border-violet-300 hover:shadow-md">
    <div class="grid h-12 w-12 shrink-0 place-items-center rounded-2xl bg-gradient-to-br {{ $grad }} text-lg text-white shadow-sm">
        {{ $icon }}
    </div>
    <div class="min-w-0 flex-1">
        <div class="flex flex-wrap items-center gap-2">
            <p class="truncate font-bold text-slate-900">{{ $campaign->title }}</p>
            <x-badge :tone="$tones[$campaign->status] ?? 'slate'">{{ ucfirst($campaign->status) }}</x-badge>
        </div>
        <p class="mt-1 truncate text-xs text-slate-500">
            {{ $campaign->products_count ?? $campaign->products->count() }} products ·
            {{ $campaign->assignments_count ?? $campaign->assignments->count() }} creators ·
            target {{ $campaign->target_creators }}
        </p>
    </div>
    <svg class="h-5 w-5 shrink-0 text-slate-300" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
</a>
