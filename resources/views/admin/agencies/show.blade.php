<x-layouts.admin :title="$agency->name">
    <a href="{{ route('admin.agencies.index') }}" class="text-sm text-slate-500 hover:text-slate-800">← Agencies</a>

    <div class="mt-2 flex flex-wrap items-start justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <h1 class="text-3xl font-black tracking-tight text-slate-900">{{ $agency->name }}</h1>
                <x-badge tone="violet">{{ $agency->plan }}</x-badge>
            </div>
            @if($agency->owner)
                <p class="mt-1 text-sm text-slate-500">Owner: <a href="{{ route('admin.users.show', $agency->owner) }}" class="text-violet-700 hover:underline">{{ $agency->owner->name }}</a> · {{ $agency->owner->email }}</p>
            @else
                <p class="mt-1 text-sm text-amber-600">⚠ No owner assigned</p>
            @endif
        </div>
        <form method="POST" action="{{ route('admin.agencies.destroy', $agency) }}" data-confirm="Delete agency {{ $agency->name }}?">
            @csrf @method('DELETE')
            <button class="btn-ghost !py-2 !text-xs !text-rose-600 hover:!bg-rose-50">Delete agency</button>
        </form>
    </div>

    {{-- KPIs --}}
    @php
        $totalCampaigns = $agency->workspaces->sum(fn ($w) => $w->campaigns()->count());
        $totalUsers     = $agency->workspaces->sum(fn ($w) => $w->users->count());
    @endphp
    <div class="mt-6 grid gap-4 sm:grid-cols-3">
        <div class="rounded-2xl bg-gradient-to-br from-violet-500 to-pink-500 p-5 text-white shadow-lg">
            <p class="text-[10px] font-bold uppercase tracking-widest opacity-90">Brands</p>
            <p class="mt-2 text-3xl font-black">{{ $agency->workspaces->count() }}</p>
        </div>
        <div class="rounded-2xl bg-gradient-to-br from-cyan-500 to-emerald-500 p-5 text-white shadow-lg">
            <p class="text-[10px] font-bold uppercase tracking-widest opacity-90">Campaigns</p>
            <p class="mt-2 text-3xl font-black">{{ number_format($totalCampaigns) }}</p>
        </div>
        <div class="rounded-2xl bg-gradient-to-br from-amber-500 to-rose-500 p-5 text-white shadow-lg">
            <p class="text-[10px] font-bold uppercase tracking-widest opacity-90">Team seats</p>
            <p class="mt-2 text-3xl font-black">{{ $totalUsers }}</p>
        </div>
    </div>

    <div class="mt-8 grid gap-6 lg:grid-cols-3">
        <section class="lg:col-span-2 rounded-2xl border border-slate-200 bg-white p-6">
            <h2 class="text-lg font-black text-slate-900">🏢 Brands in this agency</h2>
            <div class="mt-4 space-y-2">
                @forelse($agency->workspaces as $w)
                    <div class="flex items-center gap-3 rounded-xl border border-slate-100 p-3">
                        <div class="grid h-10 w-10 place-items-center rounded-lg bg-gradient-to-br from-slate-700 to-slate-900 text-sm font-bold text-white">{{ strtoupper(substr($w->name, 0, 1)) }}</div>
                        <div class="min-w-0 flex-1">
                            <a href="{{ route('admin.workspaces.show', $w) }}" class="text-sm font-semibold text-slate-900 hover:text-violet-700">{{ $w->name }}</a>
                            <p class="truncate text-xs text-slate-500">{{ $w->website ?: '—' }} · plan {{ $w->plan }} · {{ $w->users->count() }} users</p>
                        </div>
                        <form method="POST" action="{{ route('admin.agencies.detachWorkspace', [$agency, $w]) }}" data-confirm="Detach {{ $w->name }} from {{ $agency->name }}?">
                            @csrf @method('DELETE')
                            <button class="btn-ghost !py-1 !text-xs">Detach</button>
                        </form>
                    </div>
                @empty
                    <p class="text-sm text-slate-500">No brands attached yet — attach one from the right.</p>
                @endforelse
            </div>
        </section>

        <aside class="rounded-2xl border border-slate-200 bg-white p-6">
            <h2 class="text-lg font-black text-slate-900">Attach a workspace</h2>
            <form method="POST" action="{{ route('admin.agencies.attachWorkspace', $agency) }}" class="mt-3 space-y-3">
                @csrf
                <select class="input" name="workspace_id" required>
                    <option value="">— pick a workspace —</option>
                    @foreach($unassignedWorkspaces as $w)
                        <option value="{{ $w->id }}">{{ $w->name }}</option>
                    @endforeach
                </select>
                <button class="btn-primary w-full !py-2 !text-xs">Attach to agency</button>
            </form>
            <p class="mt-3 text-xs text-slate-500">Only unassigned workspaces appear here. Detach from another agency first.</p>
        </aside>
    </div>
</x-layouts.admin>
