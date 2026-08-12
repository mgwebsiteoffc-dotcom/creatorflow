<x-layouts.app panel="brand" title="Assignments">
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <h1 class="text-3xl font-black tracking-tight text-slate-900">Assignments</h1>
            <p class="mt-1 text-sm text-slate-500">Track creators from acceptance to approved content.</p>
        </div>
    </div>
    <div class="mt-6 flex flex-wrap gap-2">
        @foreach(['active' => 'Active', 'submitted' => 'Needs review', 'completed' => 'Completed', 'all' => 'All'] as $k => $label)
            <a href="{{ route('brand.assignments.index', ['status' => $k]) }}"
               class="tab-pill {{ $status === $k ? 'is-active' : '' }}">{{ $label }}</a>
        @endforeach
    </div>

    <div class="mt-6 space-y-3">
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
            <x-empty-state title="No assignments" icon="assignments">
                Launch a campaign to start assigning creators to products.
            </x-empty-state>
        @endforelse
    </div>

    <div class="mt-6">{{ $assignments->links() }}</div>
</x-layouts.app>
