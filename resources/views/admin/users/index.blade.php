<x-layouts.admin title="Users">
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <h1 class="text-3xl font-black tracking-tight text-slate-900">Users</h1>
            <p class="mt-1 text-sm text-slate-500">Manage accounts, promote admins, and ban abusive users.</p>
        </div>
    </div>

    <form method="GET" class="mt-6 flex flex-wrap gap-2">
        <input class="input max-w-xs" name="q" value="{{ request('q') }}" placeholder="Search name or email…">
        <select class="input max-w-[160px]" name="role">
            <option value="">All roles</option>
            <option value="user"       @selected(request('role')==='user')>User</option>
            <option value="admin"      @selected(request('role')==='admin')>Admin</option>
            <option value="superadmin" @selected(request('role')==='superadmin')>Superadmin</option>
        </select>
        <select class="input max-w-[160px]" name="status">
            <option value="">All statuses</option>
            <option value="suspended" @selected(request('status')==='suspended')>Suspended</option>
        </select>
        <select class="input max-w-[120px]" name="per_page" onchange="this.form.submit()">
            @foreach([25, 50, 100, 200] as $n)
                <option value="{{ $n }}" @selected((int) request('per_page', 25) === $n)>{{ $n }} / page</option>
            @endforeach
        </select>
        <button class="btn-secondary">Filter</button>
    </form>

    <div class="mt-6 overflow-x-auto rounded-2xl border border-slate-200 bg-white">
        <table class="w-full min-w-[640px] text-sm">
            <thead class="border-b border-slate-200 bg-slate-50 text-left text-xs uppercase tracking-widest text-slate-500">
                <tr><th class="p-3">User</th><th class="p-3">Role</th><th class="p-3">Status</th><th class="p-3">Workspaces</th><th class="p-3">Joined</th><th class="p-3 text-right">Actions</th></tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach($users as $u)
                    <tr>
                        <td class="p-3">
                            <a href="{{ route('admin.users.show', $u) }}" class="flex items-center gap-3 group">
                                <div class="grid h-9 w-9 place-items-center rounded-full bg-slate-100 font-bold text-slate-500">{{ strtoupper(substr($u->name,0,1)) }}</div>
                                <div>
                                    <div class="font-semibold text-slate-900 group-hover:text-violet-700">{{ $u->name }}</div>
                                    <div class="text-xs text-slate-500">{{ $u->email }}</div>
                                </div>
                            </a>
                        </td>
                        <td class="p-3">
                            @if($u->isSuperAdmin())<x-badge tone="violet">Superadmin</x-badge>
                            @elseif($u->isAdmin())<x-badge tone="violet">Admin</x-badge>
                            @else<x-badge tone="slate">User</x-badge>
                            @endif
                        </td>
                        <td class="p-3">
                            @if($u->account_status === 'suspended')
                                <x-badge tone="rose">Suspended</x-badge>
                            @else
                                <x-badge tone="green">Active</x-badge>
                            @endif
                        </td>
                        <td class="p-3 text-slate-600">{{ $u->workspaces_count }}</td>
                        <td class="p-3 text-slate-500">{{ $u->created_at->format('M j, Y') }}</td>
                        <td class="p-3 text-right">
                            <div class="flex flex-wrap justify-end gap-1">
                                @if(! $u->isSuperAdmin())
                                    @if($u->isAdmin())
                                        <form method="POST" action="{{ route('admin.users.removeAdmin', $u) }}" data-confirm="Remove admin from {{ $u->name }}?">@csrf
                                            <button class="btn-ghost !py-1 !text-xs">Demote</button>
                                        </form>
                                    @else
                                        <form method="POST" action="{{ route('admin.users.makeAdmin', $u) }}">@csrf
                                            <button class="btn-ghost !py-1 !text-xs">Make admin</button>
                                        </form>
                                    @endif
                                    @if($u->account_status === 'suspended')
                                        <form method="POST" action="{{ route('admin.users.unsuspend', $u) }}">@csrf
                                            <button class="btn-primary !py-1 !text-xs">Reactivate</button>
                                        </form>
                                    @else
                                        <form method="POST" action="{{ route('admin.users.suspend', $u) }}" data-confirm="Suspend {{ $u->name }}?">@csrf
                                            <input type="hidden" name="reason" value="Suspended by admin">
                                            <button class="btn-ghost !py-1 !text-xs !text-rose-600 hover:!bg-rose-50">Suspend</button>
                                        </form>
                                    @endif
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-6">{{ $users->links() }}</div>
</x-layouts.admin>
