<x-layouts.app panel="creator" title="Invitations">
    <h1 class="text-2xl font-bold">Invitations</h1>
    <p class="text-sm text-slate-500">Brands want to work with you.</p>

    <div class="mt-5 space-y-3">
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
