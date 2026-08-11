<x-layouts.app panel="creator" title="My work">
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <h1 class="text-3xl font-black tracking-tight text-slate-900">My work</h1>
            <p class="mt-1 text-sm text-slate-500">Active and past collaborations, all in one place.</p>
        </div>
    </div>
    <div class="mt-6 flex flex-wrap gap-2">
        @foreach(['active' => 'Active', 'completed' => 'Completed'] as $k => $label)
            <a href="{{ route('creator.assignments.index', ['status' => $k]) }}"
               class="tab-pill {{ $status === $k ? 'is-active' : '' }}">{{ $label }}</a>
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
