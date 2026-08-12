<x-layouts.admin title="Workspaces">
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <h1 class="text-3xl font-black tracking-tight text-slate-900">Brand workspaces</h1>
            <p class="mt-1 text-sm text-slate-500">Suspend brands that violate the terms.</p>
        </div>
    </div>

    <form method="GET" class="mt-6 grid gap-2 sm:grid-cols-[1fr_180px_160px_auto] sm:items-center">
        <input class="input" name="q" value="{{ request('q') }}" placeholder="Search workspaces…">
        <select class="input" name="status">
            <option value="">All</option>
            <option value="suspended" @selected(request('status')==='suspended')>Suspended</option>
        </select>
        <select class="input" name="per_page" onchange="this.form.submit()">
            @foreach([25, 50, 100, 200] as $n)
                <option value="{{ $n }}" @selected((int) request('per_page', 25) === $n)>{{ $n }} / page</option>
            @endforeach
        </select>
        <button class="btn-secondary">Filter</button>
    </form>

    <div class="mt-6 overflow-x-auto rounded-2xl border border-slate-200 bg-white">
        <table class="w-full min-w-[640px] text-sm">
            <thead class="border-b border-slate-200 bg-slate-50 text-left text-xs uppercase tracking-widest text-slate-500">
                <tr><th class="p-3">Workspace</th><th class="p-3">Plan</th><th class="p-3">Users</th><th class="p-3">Campaigns</th><th class="p-3">Products</th><th class="p-3">Status</th><th class="p-3 text-right">Actions</th></tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach($workspaces as $w)
                    <tr>
                        <td class="p-3">
                            <a href="{{ route('admin.workspaces.show', $w) }}" class="group">
                                <span class="font-semibold text-slate-900 group-hover:text-violet-700">{{ $w->name }}</span><br>
                                <span class="text-xs text-slate-500">{{ $w->website ?: '—' }}</span>
                            </a>
                        </td>
                        <td class="p-3"><x-badge tone="violet">{{ $w->plan }}</x-badge></td>
                        <td class="p-3 text-slate-600">{{ $w->users_count }}</td>
                        <td class="p-3 text-slate-600">{{ $w->campaigns_count }}</td>
                        <td class="p-3 text-slate-600">{{ $w->products_count }}</td>
                        <td class="p-3">
                            @if($w->account_status === 'suspended')<x-badge tone="rose">Suspended</x-badge>
                            @else <x-badge tone="green">Active</x-badge>@endif
                        </td>
                        <td class="p-3 text-right">
                            @if($w->account_status === 'suspended')
                                <form method="POST" action="{{ route('admin.workspaces.reinstate', $w) }}">@csrf
                                    <button class="btn-primary !py-1 !text-xs">Reinstate</button>
                                </form>
                            @else
                                <form method="POST" action="{{ route('admin.workspaces.suspend', $w) }}" data-confirm="Suspend {{ $w->name }}?">@csrf
                                    <input type="hidden" name="reason" value="Suspended by admin">
                                    <button class="btn-ghost !py-1 !text-xs !text-rose-600 hover:!bg-rose-50">Suspend</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-6">{{ $workspaces->links() }}</div>
</x-layouts.admin>
