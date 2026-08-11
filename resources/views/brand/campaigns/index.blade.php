<x-layouts.app panel="brand" title="Campaigns">
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <h1 class="text-3xl font-black tracking-tight text-slate-900">Campaigns</h1>
            <p class="mt-1 text-sm text-slate-500">Every campaign you've drafted, launched, or completed.</p>
        </div>
        <a href="{{ route('brand.campaigns.create') }}" class="btn-primary !py-2 text-sm">+ New campaign</a>
    </div>

    <div class="mt-8 space-y-3">
        @forelse($campaigns as $campaign)
            @include('brand.campaigns._card', ['campaign' => $campaign])
        @empty
            <x-empty-state title="No campaigns yet" icon="🚀">
                AI can draft your first campaign from your products in seconds.
                <x-slot:action><a href="{{ route('brand.campaigns.create') }}" class="btn-primary">Create campaign</a></x-slot:action>
            </x-empty-state>
        @endforelse
    </div>

    <div class="mt-8">{{ $campaigns->links() }}</div>
</x-layouts.app>
