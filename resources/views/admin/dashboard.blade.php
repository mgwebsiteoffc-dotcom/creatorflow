<x-layouts.admin title="Dashboard">
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <h1 class="text-3xl font-black tracking-tight text-slate-900">Admin dashboard</h1>
            <p class="mt-1 text-sm text-slate-500">System-wide health, revenue and pending actions.</p>
        </div>
    </div>

    <div class="mt-8 grid gap-4 md:grid-cols-3 lg:grid-cols-4">
        <x-stat label="Users"       :value="number_format($stats['users'])"      tone="violet"/>
        <x-stat label="Creators"    :value="number_format($stats['creators'])"   tone="rose"/>
        <x-stat label="Workspaces"  :value="number_format($stats['workspaces'])" tone="sky"/>
        <x-stat label="Campaigns"   :value="number_format($stats['campaigns'])"  tone="amber"/>
        <x-stat label="Applications":value="number_format($stats['applications'])" tone="emerald"/>
        <x-stat label="New leads"   :value="number_format($stats['leads_new'])"  tone="rose"/>
        <x-stat label="Blog posts"  :value="number_format($stats['blog_posts'])" tone="slate"/>
        <x-stat label="Paid GMV"    :value="'$'.number_format($stats['gmv_cents']/100)" tone="emerald"/>
    </div>

    <div class="mt-8 grid gap-6 lg:grid-cols-3">
        <div class="lg:col-span-2 rounded-2xl border border-slate-200 bg-white p-5 md:p-6">
            <h2 class="text-lg font-bold text-slate-900">Latest leads</h2>
            <div class="mt-4 space-y-2">
                @forelse($recentLeads as $l)
                    <a href="{{ route('admin.leads.index') }}" class="flex items-center gap-3 rounded-xl border border-slate-100 p-3 hover:border-violet-300">
                        <div class="grid h-9 w-9 place-items-center rounded-full bg-slate-100 font-bold text-slate-500">{{ strtoupper(substr($l->name, 0, 1)) }}</div>
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-semibold">{{ $l->name }} <span class="text-xs font-normal text-slate-500">· {{ $l->email }}</span></p>
                            <p class="truncate text-xs text-slate-500">{{ $l->company ?: '—' }} · {{ ucfirst($l->reason ?? '') }} · {{ $l->created_at->diffForHumans() }}</p>
                        </div>
                        <x-badge :tone="match($l->status) { 'new' => 'amber', 'qualified' => 'sky', 'won' => 'green', 'lost' => 'rose', default => 'slate' }">{{ $l->status }}</x-badge>
                    </a>
                @empty
                    <p class="text-sm text-slate-500">No leads yet — waiting on your first contact-form submit.</p>
                @endforelse
            </div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-5 md:p-6">
            <h2 class="text-lg font-bold text-slate-900">Escrow held</h2>
            <div class="mt-2 text-4xl font-black text-slate-900">${{ number_format($stats['escrow_held']/100, 2) }}</div>
            <p class="mt-1 text-xs text-slate-500">Currently held for pending payouts.</p>
            <a href="{{ route('admin.escrow.index') }}" class="mt-4 inline-block text-sm font-semibold text-violet-700 hover:text-violet-900">Manage escrow →</a>
        </div>
    </div>

    <div class="mt-8 grid gap-6 lg:grid-cols-2">
        <div class="rounded-2xl border border-slate-200 bg-white p-5 md:p-6">
            <h2 class="text-lg font-bold text-slate-900">Recent users</h2>
            <div class="mt-4 space-y-2">
                @foreach($recentUsers as $u)
                    <div class="flex items-center gap-3 rounded-xl border border-slate-100 p-3">
                        <div class="grid h-9 w-9 place-items-center rounded-full bg-slate-100 font-bold text-slate-500">{{ strtoupper(substr($u->name,0,1)) }}</div>
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-semibold">{{ $u->name }} <span class="text-xs font-normal text-slate-500">· {{ $u->email }}</span></p>
                            <p class="text-xs text-slate-500">Joined {{ $u->created_at->diffForHumans() }}</p>
                        </div>
                        @if($u->isSuperAdmin())<x-badge tone="violet">Superadmin</x-badge>
                        @elseif($u->isAdmin())<x-badge tone="violet">Admin</x-badge>
                        @elseif($u->account_status === 'suspended')<x-badge tone="rose">Suspended</x-badge>
                        @else<x-badge tone="slate">User</x-badge>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-5 md:p-6">
            <h2 class="text-lg font-bold text-slate-900">Recent creators</h2>
            <div class="mt-4 space-y-2">
                @foreach($recentCreators as $c)
                    <div class="flex items-center gap-3 rounded-xl border border-slate-100 p-3">
                        <div class="grid h-9 w-9 place-items-center rounded-full bg-gradient-to-br from-rose-500 to-pink-500 font-bold text-white">{{ strtoupper(substr($c->display_name,0,1)) }}</div>
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-semibold">{{ $c->display_name }}</p>
                            <p class="text-xs text-slate-500">{{ number_format($c->follower_count_total) }} followers · {{ $c->engagement_rate }}% ER</p>
                        </div>
                        <x-badge :tone="$c->status === 'active' ? 'green' : ($c->status === 'suspended' ? 'rose' : 'slate')">{{ $c->status }}</x-badge>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</x-layouts.admin>
