@props(['c', 'applied' => false])
<a href="{{ route('creator.marketplace.show', $c) }}" class="card overflow-hidden hover:border-rose-300 relative">
    @if($applied)
        <span class="absolute right-3 top-3 z-10 rounded-full bg-emerald-100 px-2 py-0.5 text-[10px] font-bold uppercase tracking-widest text-emerald-700 ring-1 ring-emerald-200">✓ Applied</span>
    @endif
    <div class="flex gap-3 p-4">
        <div class="grid h-14 w-14 shrink-0 place-items-center rounded-xl bg-gradient-to-br from-violet-500 to-rose-500 text-white">
            {{ match($c->type) { 'barter' => '🎁', 'paid' => '💸', 'affiliate' => '🔗', default => '🚀' } }}
        </div>
        <div class="min-w-0 flex-1">
            <p class="text-xs text-slate-500">{{ $c->workspace->name }}</p>
            <p class="truncate font-semibold">{{ $c->title }}</p>
            <p class="mt-0.5 line-clamp-2 text-xs text-slate-500">{{ $c->summary }}</p>
        </div>
    </div>
    @if($c->products->isNotEmpty())
        <div class="flex -space-x-2 border-t border-slate-100 px-4 py-2">
            @foreach($c->products->take(4) as $cp)
                <div class="grid h-8 w-8 place-items-center rounded-full bg-slate-100 text-xs">📦</div>
            @endforeach
            <span class="ml-3 self-center text-xs text-slate-500">{{ $c->products->count() }} products · {{ $c->target_creators }} creators</span>
        </div>
    @endif
</a>
