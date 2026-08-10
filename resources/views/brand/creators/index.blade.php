<x-layouts.app panel="brand" title="Creator marketplace">
    <h1 class="text-2xl font-bold">Creator marketplace</h1>
    <p class="text-sm text-slate-500">Discover and invite creators. Scores reflect niche fit, engagement and performance.</p>

    <form method="GET" class="card mt-5 flex flex-wrap items-end gap-3 p-4">
        <div class="min-w-[180px] flex-1">
            <label class="label">Niche</label>
            <select class="input" name="niche">
                <option value="">All niches</option>
                @foreach($niches as $n)
                    <option value="{{ $n }}" @selected(request('niche') === $n)>{{ $n }}</option>
                @endforeach
            </select>
        </div>
        <div class="w-36">
            <label class="label">Min followers</label>
            <input class="input" type="number" name="min_followers" value="{{ request('min_followers') }}" placeholder="10000">
        </div>
        <div class="w-32">
            <label class="label">Min eng. %</label>
            <input class="input" type="number" step="0.1" name="min_engagement" value="{{ request('min_engagement') }}" placeholder="3">
        </div>
        <label class="flex items-center gap-2 pb-2 text-sm">
            <input type="checkbox" name="barter" value="1" @checked(request('barter'))> Barter only
        </label>
        <button class="btn-primary">Filter</button>
    </form>

    <div class="mt-5 grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
        @forelse($creators as $creator)
            @include('brand.creators._card', ['creator' => $creator])
        @empty
            <x-empty-state title="No creators match" icon="🎬" class="sm:col-span-2 lg:col-span-3">
                Try widening your filters.
            </x-empty-state>
        @endforelse
    </div>

    <div class="mt-6">{{ $creators->links() }}</div>
</x-layouts.app>
