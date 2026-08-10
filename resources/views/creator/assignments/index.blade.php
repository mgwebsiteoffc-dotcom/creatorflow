<x-layouts.app panel="creator" title="My work">
    <h1 class="text-2xl font-bold">My work</h1>
    <div class="mt-3 flex gap-2 text-sm">
        @foreach(['active' => 'Active', 'completed' => 'Completed'] as $k => $label)
            <a href="{{ route('creator.assignments.index', ['status' => $k]) }}"
               class="rounded-full px-3 py-1.5 @if($status === $k) bg-rose-600 text-white @else bg-slate-100 text-slate-600 @endif">{{ $label }}</a>
        @endforeach
    </div>

    <div class="mt-5 space-y-3">
        @forelse($assignments as $a)
            <a href="{{ route('creator.assignments.show', $a) }}" class="card flex items-center gap-3 p-4">
                <div class="grid h-11 w-11 place-items-center rounded-xl bg-violet-100 text-lg">
                    {{ match(true) { in_array($a->status, ['shipped','delivered']) => '🚚', in_array($a->status, ['submitted','changes_requested']) => '⏳', in_array($a->status, ['approved','completed']) => '✅', default => '📦' } }}
                </div>
                <div class="min-w-0 flex-1">
                    <p class="truncate text-sm font-semibold">{{ $a->campaign->title }}</p>
                    <p class="truncate text-xs text-slate-500">{{ $a->campaignProduct->product->title ?? '' }} · {{ str_replace('_',' ', $a->status) }}</p>
                </div>
                <svg class="h-5 w-5 text-slate-300" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
            </a>
        @empty
            <x-empty-state title="Nothing here yet" icon="🎬">
                Accepted campaigns and your deliverables show up here.
            </x-empty-state>
        @endforelse
    </div>

    <div class="mt-6">{{ $assignments->links() }}</div>
</x-layouts.app>
