<x-layouts.app panel="brand" title="Creator applications">
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <h1 class="text-3xl font-black tracking-tight text-slate-900">Creator applications</h1>
            <p class="mt-1 text-sm text-slate-500">Creators who found your campaigns and applied. Review, shortlist, or approve.</p>
        </div>
        <a href="{{ route('brand.creators.index') }}" class="btn-secondary !py-2 text-sm">Browse creators</a>
    </div>

    @php
        $tabs = [
            'submitted'   => ['label' => 'New',         'tone' => 'amber'],
            'shortlisted' => ['label' => 'Shortlisted', 'tone' => 'sky'],
            'approved'    => ['label' => 'Approved',    'tone' => 'green'],
            'rejected'    => ['label' => 'Rejected',    'tone' => 'rose'],
            'all'         => ['label' => 'All',         'tone' => 'slate'],
        ];
    @endphp
    <div class="mt-6 flex flex-wrap gap-2">
        @foreach($tabs as $key => $t)
            <a href="{{ route('brand.applications.index', ['status' => $key]) }}"
               class="tab-pill {{ $status === $key ? 'is-active' : '' }}">
                {{ $t['label'] }}
                <span class="ml-1 rounded-full bg-black/10 px-1.5 py-0.5 text-[10px] font-bold {{ $status === $key ? 'text-white' : 'text-slate-500' }}">{{ $counts[$key] ?? 0 }}</span>
            </a>
        @endforeach
    </div>

    <div class="mt-6 space-y-3">
        @forelse($applications as $app)
            @include('brand.applications._row', ['app' => $app])
        @empty
            <x-empty-state title="No applications in this bucket" icon="📥">
                Creators discover your campaigns from the marketplace. Once you launch, applications start rolling in here.
            </x-empty-state>
        @endforelse
    </div>

    <div class="mt-6">{{ $applications->links() }}</div>
</x-layouts.app>
