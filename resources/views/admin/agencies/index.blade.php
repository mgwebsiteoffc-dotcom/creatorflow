<x-layouts.admin title="Agencies">
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <p class="text-xs font-bold uppercase tracking-widest text-violet-600">Superadmin</p>
            <h1 class="mt-1 text-3xl font-black tracking-tight text-slate-900">🏢 Agencies</h1>
            <p class="mt-1 text-sm text-slate-500">Group multiple brand workspaces under one agency. Agency owners can see spend + creators across all of their brands.</p>
        </div>
        <a href="{{ route('admin.agencies.create') }}" class="btn-gradient !py-2 text-sm">+ New agency</a>
    </div>

    <form method="GET" class="mt-6 grid gap-2 sm:grid-cols-[1fr_160px_auto] sm:items-center">
        <input class="input" name="q" value="{{ request('q') }}" placeholder="Search agency…">
        <select class="input" name="per_page" onchange="this.form.submit()">
            @foreach([25, 50, 100, 200] as $n)<option value="{{ $n }}" @selected((int) request('per_page', 25) === $n)>{{ $n }} / page</option>@endforeach
        </select>
        <button class="btn-secondary">Filter</button>
    </form>

    <div class="mt-6 overflow-x-auto rounded-2xl border border-slate-200 bg-white">
        <table class="w-full min-w-[640px] text-sm">
            <thead class="border-b border-slate-200 bg-slate-50 text-left text-xs uppercase tracking-widest text-slate-500">
                <tr><th class="p-3">Agency</th><th class="p-3">Owner</th><th class="p-3">Plan</th><th class="p-3">Brands</th><th class="p-3">Created</th><th class="p-3 text-right">Actions</th></tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($agencies as $a)
                    <tr class="hover:bg-violet-50/30">
                        <td class="p-3">
                            <a href="{{ route('admin.agencies.show', $a) }}" class="font-black text-slate-900 hover:text-violet-700">{{ $a->name }}</a>
                        </td>
                        <td class="p-3 text-sm text-slate-700">
                            @if($a->owner)
                                <div class="font-semibold">{{ $a->owner->name }}</div>
                                <div class="text-xs text-slate-500">{{ $a->owner->email }}</div>
                            @else — @endif
                        </td>
                        <td class="p-3"><x-badge tone="violet">{{ $a->plan }}</x-badge></td>
                        <td class="p-3 text-slate-600">{{ $a->workspaces_count }}</td>
                        <td class="p-3 text-xs text-slate-500">{{ $a->created_at->format('M j, Y') }}</td>
                        <td class="p-3 text-right">
                            <form method="POST" action="{{ route('admin.agencies.destroy', $a) }}" data-confirm="Delete agency {{ $a->name }}? Workspaces are detached but kept.">
                                @csrf @method('DELETE')
                                <button class="btn-ghost !py-1 !text-xs !text-rose-600 hover:!bg-rose-50">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="p-8 text-center text-sm text-slate-500">No agencies yet. <a href="{{ route('admin.agencies.create') }}" class="text-violet-700 hover:underline">Create the first one →</a></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">{{ $agencies->links() }}</div>
</x-layouts.admin>
