<x-layouts.app panel="brand" title="Campaigns">
    <div class="mb-6 flex items-center justify-between">
        <h1 class="text-2xl font-bold">Campaigns</h1>
        <a href="{{ route('brand.campaigns.create') }}" class="btn-primary text-sm">+ New campaign</a>
    </div>

    @forelse($campaigns as $campaign)
        @include('brand.campaigns._card', ['campaign' => $campaign])
    @empty
        <x-empty-state title="No campaigns yet" icon="🚀">
            AI can draft your first campaign from your products in seconds.
            <x-slot:action><a href="{{ route('brand.campaigns.create') }}" class="btn-primary">Create campaign</a></x-slot:action>
        </x-empty-state>
    @endforelse

    <div class="mt-6">{{ $campaigns->links() }}</div>
</x-layouts.app>
