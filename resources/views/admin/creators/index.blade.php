<x-layouts.admin title="Creators">
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <h1 class="text-3xl font-black tracking-tight text-slate-900">Creators</h1>
            <p class="mt-1 text-sm text-slate-500">Verify, ban, and bulk-import creators.</p>
        </div>
        <a href="{{ route('admin.creators.import') }}" class="btn-primary !py-2 text-sm">📥 Bulk import (CSV)</a>
    </div>

    <form method="GET" class="mt-6 flex flex-wrap gap-2">
        <input class="input max-w-xs" name="q" value="{{ request('q') }}" placeholder="Search creator…">
        <select class="input max-w-[160px]" name="status">
            <option value="">All statuses</option>
            @foreach(['active','pending','suspended'] as $s)<option value="{{ $s }}" @selected(request('status')===$s)>{{ ucfirst($s) }}</option>@endforeach
        </select>
        <button class="btn-secondary">Filter</button>
    </form>

    <div class="mt-6 overflow-hidden rounded-2xl border border-slate-200 bg-white">
        <table class="w-full text-sm">
            <thead class="border-b border-slate-200 bg-slate-50 text-left text-xs uppercase tracking-widest text-slate-500">
                <tr><th class="p-3">Creator</th><th class="p-3">Followers</th><th class="p-3">Status</th><th class="p-3">Assignments</th><th class="p-3 text-right">Actions</th></tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach($creators as $c)
                    <tr>
                        <td class="p-3">
                            <div class="flex items-center gap-3">
                                <div class="grid h-9 w-9 place-items-center rounded-full bg-gradient-to-br from-rose-500 to-pink-500 font-bold text-white">{{ strtoupper(substr($c->display_name,0,1)) }}</div>
                                <div>
                                    <div class="font-semibold text-slate-900">{{ $c->display_name }}</div>
                                    <div class="text-xs text-slate-500">{{ $c->email ?: '—' }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="p-3 text-slate-600">{{ number_format($c->follower_count_total) }}</td>
                        <td class="p-3">
                            <x-badge :tone="$c->status === 'active' ? 'green' : ($c->status === 'suspended' ? 'rose' : 'amber')">{{ $c->status }}</x-badge>
                        </td>
                        <td class="p-3 text-slate-600">{{ $c->assignments_count }}</td>
                        <td class="p-3 text-right">
                            @if($c->status === 'suspended')
                                <form method="POST" action="{{ route('admin.creators.reinstate', $c) }}">@csrf
                                    <button class="btn-primary !py-1 !text-xs">Reinstate</button>
                                </form>
                            @else
                                <form method="POST" action="{{ route('admin.creators.suspend', $c) }}" data-confirm="Ban {{ $c->display_name }}?">@csrf
                                    <input type="hidden" name="reason" value="Banned by admin">
                                    <button class="btn-ghost !py-1 !text-xs !text-rose-600 hover:!bg-rose-50">Ban</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-6">{{ $creators->links() }}</div>
</x-layouts.admin>
