<x-layouts.app panel="brand" title="Assignments">
    <h1 class="text-2xl font-bold">Assignments</h1>
    <div class="mt-3 flex gap-2 text-sm">
        @foreach(['active' => 'Active', 'submitted' => 'Needs review', 'completed' => 'Completed', 'all' => 'All'] as $k => $label)
            <a href="{{ route('brand.assignments.index', ['status' => $k]) }}"
               class="rounded-full px-3 py-1.5 @if($status === $k) bg-violet-600 text-white @else bg-slate-100 text-slate-600 @endif">{{ $label }}</a>
        @endforeach
    </div>

    <div class="mt-5 space-y-2">
        @forelse($assignments as $a)
            <a href="{{ route('brand.assignments.show', $a) }}" class="card flex items-center gap-3 p-3">
                <div class="grid h-10 w-10 place-items-center rounded-full bg-rose-100 font-bold text-rose-700">{{ strtoupper(substr($a->creator->display_name,0,1)) }}</div>
                <div class="min-w-0 flex-1">
                    <p class="truncate text-sm font-semibold">{{ $a->creator->display_name }}</p>
                    <p class="truncate text-xs text-slate-500">{{ $a->campaign->title }} · {{ $a->campaignProduct->product->title ?? '' }}</p>
                </div>
                <x-badge :tone="in_array($a->status,['approved','completed']) ? 'green' : (in_array($a->status,['submitted','changes_requested']) ? 'amber' : 'slate')">
                    {{ str_replace('_',' ', $a->status) }}
                </x-badge>
            </a>
        @empty
            <x-empty-state title="No assignments" icon="📋">
                Launch a campaign to start assigning creators to products.
            </x-empty-state>
        @endforelse
    </div>

    <div class="mt-6">{{ $assignments->links() }}</div>
</x-layouts.app>
