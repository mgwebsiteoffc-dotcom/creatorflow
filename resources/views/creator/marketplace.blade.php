<x-layouts.app panel="creator" title="Marketplace">
    <h1 class="text-2xl font-bold">Find campaigns</h1>
    <p class="text-sm text-slate-500">Brands looking for creators like you.</p>

    <form method="GET" class="mt-4 flex gap-2">
        <select class="input max-w-xs" name="type">
            <option value="">All types</option>
            @foreach(['barter' => 'Barter / gifting', 'paid' => 'Paid', 'affiliate' => 'Affiliate'] as $v => $l)
                <option value="{{ $v }}" @selected(request('type') === $v)>{{ $l }}</option>
            @endforeach
        </select>
        <button class="btn-secondary">Filter</button>
    </form>

    <div class="mt-5 grid gap-4 sm:grid-cols-2">
        @forelse($campaigns as $c)
            @include('creator._campaign-card', ['c' => $c])
        @empty
            <x-empty-state title="No campaigns right now" icon="🔍" class="sm:col-span-2">
                New campaigns are posted daily. Check back soon or make sure your profile is complete.
            </x-empty-state>
        @endforelse
    </div>

    <div class="mt-6">{{ $campaigns->links() }}</div>
</x-layouts.app>
