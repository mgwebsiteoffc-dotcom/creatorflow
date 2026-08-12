<x-layouts.app panel="creator" title="Marketplace">
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <h1 class="text-3xl font-black tracking-tight text-slate-900">Find campaigns</h1>
            <p class="mt-1 text-sm text-slate-500">Brands looking for creators like you.</p>
        </div>
        <a href="{{ route('creator.applications') }}" class="btn-secondary !py-2 text-sm">My applications →</a>
    </div>

    <form method="GET" class="mt-6 flex gap-2"
          data-skeleton-target="#marketplace-list"
          data-skeleton-slot="#marketplace-list-skeleton">
        <select class="input max-w-xs" name="type">
            <option value="">All types</option>
            @foreach(['barter' => 'Barter / gifting', 'paid' => 'Paid', 'affiliate' => 'Affiliate'] as $v => $l)
                <option value="{{ $v }}" @selected(request('type') === $v)>{{ $l }}</option>
            @endforeach
        </select>
        <button class="btn-secondary">Filter</button>
    </form>

    <div id="marketplace-list" class="mt-5 grid gap-4 sm:grid-cols-2 transition-opacity">
        @forelse($campaigns as $c)
            @include('creator._campaign-card', ['c' => $c, 'applied' =>in_array($c->id, $appliedIds ?? [])])
        @empty
            <x-empty-state title="No campaigns right now" icon="campaigns" class="sm:col-span-2">
                New campaigns are posted daily. Check back soon or make sure your profile is complete.
                <x-slot:action><a href="{{ route('creator.profile.edit') }}" class="btn-primary">Polish your profile</a></x-slot:action>
            </x-empty-state>
        @endforelse
    </div>

    <x-skeleton-card id="marketplace-list-skeleton" class="mt-5 hidden !grid-cols-1 sm:!grid-cols-2 lg:!grid-cols-2" :count="4" />

    <div class="mt-6" data-skeleton-container="#marketplace-list">{{ $campaigns->links() }}</div>
</x-layouts.app>
