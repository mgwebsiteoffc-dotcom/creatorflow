<x-layouts.app panel="creator" title="Invitations">
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <h1 class="text-3xl font-black tracking-tight text-slate-900">Invitations</h1>
            <p class="mt-1 text-sm text-slate-500">Brands want to work with you.</p>
        </div>
    </div>

    <div class="mt-6 space-y-3">
        @forelse($invitations as $inv)
            @include('creator._invitation', ['inv' => $inv])
        @empty
            <x-empty-state title="No pending invitations" icon="📭">
                <a href="{{ route('creator.marketplace') }}" class="font-medium text-rose-600">Browse the marketplace →</a>
            </x-empty-state>
        @endforelse
    </div>

    <div class="mt-6">{{ $invitations->links() }}</div>
</x-layouts.app>
