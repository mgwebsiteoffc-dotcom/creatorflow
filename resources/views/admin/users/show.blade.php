<x-layouts.admin :title="$user->name">
    <a href="{{ route('admin.users.index') }}" class="text-sm text-slate-500 hover:text-slate-800">← Users</a>

    {{-- HEADER --}}
    <div class="mt-3 overflow-hidden rounded-3xl border border-slate-200 bg-white">
        <div class="relative h-24"
             style="background-image: linear-gradient(120deg,#7c3aed 0%,#ec4899 50%,#f59e0b 110%);"></div>
        <div class="relative px-6 pb-6 pt-0">
            <div class="-mt-10 flex flex-wrap items-end justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="grid h-20 w-20 shrink-0 place-items-center rounded-2xl bg-white text-2xl font-black text-slate-800 shadow-lg ring-4 ring-white">
                        {{ strtoupper(substr($user->name, 0, 2)) }}
                    </div>
                    <div class="pt-8">
                        <h1 class="text-2xl font-black tracking-tight text-slate-900">{{ $user->name }}</h1>
                        <p class="text-sm text-slate-500">{{ $user->email }} · joined {{ $user->created_at->diffForHumans() }}</p>
                    </div>
                </div>
                <div class="flex flex-wrap gap-2 pt-8">
                    @if($user->isSuperAdmin())<x-badge tone="violet">Superadmin</x-badge>
                    @elseif($user->isAdmin())<x-badge tone="violet">Admin</x-badge>
                    @endif
                    @if($user->account_status === 'suspended')
                        <x-badge tone="rose">Suspended</x-badge>
                    @else
                        <x-badge tone="green">Active</x-badge>
                    @endif
                </div>
            </div>

            {{-- Quick stats --}}
            <div class="mt-6 grid gap-3 md:grid-cols-4">
                <div class="rounded-2xl border border-slate-100 p-3">
                    <div class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Workspaces</div>
                    <div class="mt-1 text-2xl font-black text-slate-900">{{ $user->workspaces->count() }}</div>
                </div>
                <div class="rounded-2xl border border-slate-100 p-3">
                    <div class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Creator profile</div>
                    <div class="mt-1 text-lg font-black text-slate-900">{{ $user->creator ? 'Yes' : 'No' }}</div>
                </div>
                <div class="rounded-2xl border border-slate-100 p-3">
                    <div class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Last login</div>
                    <div class="mt-1 text-sm font-bold text-slate-900">{{ $user->last_login_at?->diffForHumans() ?? '—' }}</div>
                </div>
                <div class="rounded-2xl border border-slate-100 p-3">
                    <div class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Payments logged</div>
                    <div class="mt-1 text-2xl font-black text-slate-900">{{ $paymentRecords->count() }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- ACTIONS --}}
    <section class="mt-8 rounded-2xl border border-slate-200 bg-white p-6">
        <h2 class="text-lg font-bold text-slate-900">Actions</h2>
        <p class="mt-1 text-xs text-slate-500">System-owner controls. Superadmins cannot be modified.</p>
        <div class="mt-4 flex flex-wrap gap-2">
            @if(! $user->isSuperAdmin())
                @if($user->isAdmin())
                    <form method="POST" action="{{ route('admin.users.removeAdmin', $user) }}" data-confirm="Remove admin from {{ $user->name }}?">@csrf
                        <button class="btn-ghost !py-1.5 text-xs">Demote from admin</button>
                    </form>
                @else
                    <form method="POST" action="{{ route('admin.users.makeAdmin', $user) }}">@csrf
                        <button class="btn-ghost !py-1.5 text-xs">Promote to admin</button>
                    </form>
                @endif
                @if($user->account_status === 'suspended')
                    <form method="POST" action="{{ route('admin.users.unsuspend', $user) }}">@csrf
                        <button class="btn-primary !py-1.5 text-xs">Reactivate account</button>
                    </form>
                @else
                    <form method="POST" action="{{ route('admin.users.suspend', $user) }}" data-confirm="Suspend {{ $user->name }}?">@csrf
                        <input type="hidden" name="reason" value="Suspended by admin">
                        <button class="btn-ghost !py-1.5 text-xs !text-rose-600 hover:!bg-rose-50">Suspend account</button>
                    </form>
                @endif
            @else
                <span class="text-xs text-slate-500">Superadmin — no actions available.</span>
            @endif
        </div>
    </section>

    <div class="mt-8 grid gap-6 lg:grid-cols-2">
        {{-- WORKSPACES --}}
        <section class="rounded-2xl border border-slate-200 bg-white p-6">
            <h2 class="text-lg font-bold text-slate-900">🏢 Workspaces</h2>
            <div class="mt-4 space-y-2">
                @forelse($user->workspaces as $w)
                    <a href="{{ route('admin.workspaces.show', $w) }}" class="flex items-center justify-between rounded-xl border border-slate-100 p-3 hover:border-violet-300">
                        <div>
                            <div class="text-sm font-semibold text-slate-900">{{ $w->name }}</div>
                            <div class="text-xs text-slate-500">{{ ucfirst($w->pivot->role ?? 'member') }} · plan {{ $w->plan }}</div>
                        </div>
                        <span class="text-slate-300">↗</span>
                    </a>
                @empty
                    <p class="text-sm text-slate-500">This user isn't a member of any workspace.</p>
                @endforelse
            </div>
        </section>

        {{-- CREATOR PROFILE --}}
        <section class="rounded-2xl border border-slate-200 bg-white p-6">
            <h2 class="text-lg font-bold text-slate-900">🎬 Creator profile</h2>
            @if($user->creator)
                <a href="{{ route('admin.creators.show', $user->creator) }}" class="mt-4 flex items-center justify-between rounded-xl border border-slate-100 p-3 hover:border-violet-300">
                    <div>
                        <div class="text-sm font-semibold text-slate-900">{{ $user->creator->display_name }}</div>
                        <div class="text-xs text-slate-500">{{ $user->creator->status }} · {{ number_format($user->creator->follower_count_total) }} followers</div>
                    </div>
                    <span class="text-slate-300">↗</span>
                </a>
            @else
                <p class="mt-4 text-sm text-slate-500">This user hasn't created a creator profile.</p>
            @endif
        </section>
    </div>

    {{-- RECENT ACTIVITY --}}
    <section class="mt-8 rounded-2xl border border-slate-200 bg-white p-6">
        <h2 class="text-lg font-bold text-slate-900">🔔 Recent notifications</h2>
        <div class="mt-4 space-y-2">
            @forelse($notifications as $n)
                <div class="flex items-start gap-3 rounded-xl border border-slate-100 p-3 text-sm">
                    <span class="text-lg">{{ str_contains($n->type, 'approved') ? '✅' : (str_contains($n->type, 'content') ? '🎬' : '🔔') }}</span>
                    <div class="min-w-0 flex-1">
                        <div class="font-semibold text-slate-900">{{ $n->data['title'] ?? $n->type }}</div>
                        @if($n->data['body'] ?? null)<div class="text-xs text-slate-500">{{ $n->data['body'] }}</div>@endif
                        <div class="mt-1 text-[11px] text-slate-400">{{ $n->created_at->diffForHumans() }}</div>
                    </div>
                    @if(! $n->read_at)<span class="rounded-full bg-violet-100 px-2 py-0.5 text-[10px] font-bold text-violet-700">unread</span>@endif
                </div>
            @empty
                <p class="text-sm text-slate-500">No notifications yet.</p>
            @endforelse
        </div>
    </section>

    {{-- PAYMENTS RECORDED BY THIS USER --}}
    @if($paymentRecords->isNotEmpty())
        <section class="mt-8 rounded-2xl border border-slate-200 bg-white p-6">
            <h2 class="text-lg font-bold text-slate-900">💳 Payments recorded by this user</h2>
            <div class="mt-4 overflow-x-auto rounded-xl border border-slate-100">
                <table class="w-full min-w-[640px] text-sm">
                    <thead class="border-b border-slate-100 bg-slate-50 text-left text-xs uppercase tracking-widest text-slate-500">
                        <tr><th class="p-3">When</th><th class="p-3">Workspace</th><th class="p-3">Description</th><th class="p-3 text-right">Amount</th><th class="p-3 text-right">Status</th></tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($paymentRecords as $p)
                            <tr>
                                <td class="p-3 text-xs text-slate-500">{{ optional($p->paid_at ?: $p->created_at)->format('M j, Y') }}</td>
                                <td class="p-3 text-slate-800">{{ $p->workspace?->name ?? '—' }}</td>
                                <td class="p-3">{{ $p->description ?: ucfirst($p->kind) }}</td>
                                <td class="p-3 text-right font-mono font-semibold text-slate-900">{{ ($p->workspace ?? new \App\Models\Workspace)->formatMoney((int) $p->amount_cents, $p->currency) }}</td>
                                <td class="p-3 text-right"><x-badge :tone="$p->status === 'succeeded' ? 'green' : ($p->status === 'refunded' ? 'sky' : 'amber')">{{ $p->status }}</x-badge></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>
    @endif
</x-layouts.admin>
