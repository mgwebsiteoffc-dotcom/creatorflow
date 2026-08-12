<x-layouts.app panel="creator" title="Marketplace">
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <h1 class="text-3xl font-black tracking-tight text-slate-900">Find campaigns</h1>
            <p class="mt-1 text-sm text-slate-500">Brands looking for creators like you.</p>
        </div>
        <a href="{{ route('creator.applications') }}" class="btn-secondary !py-2 text-sm">My applications →</a>
    </div>

    <form method="GET" class="mt-6 flex gap-2">
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
            @include('creator._campaign-card', ['c' => $c, 'applied' =>in_array($c->id, $appliedIds ?? [])])
        @empty
            <x-empty-state title="No campaigns right now" icon="campaigns" class="sm:col-span-2">
                New campaigns are posted daily. Check back soon or make sure your profile is complete.
            </x-empty-state>
        @endforelse
    </div>

    <div class="mt-6">{{ $campaigns->links() }}</div>
</x-layouts.app>
