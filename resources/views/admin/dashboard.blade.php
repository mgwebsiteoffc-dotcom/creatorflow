<x-layouts.admin title="Dashboard">
    {{-- Page header --}}
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <p class="text-xs font-bold uppercase tracking-widest text-violet-600">Platform overview</p>
            <h1 class="mt-1 text-3xl font-black tracking-tight text-slate-900">Welcome back, {{ auth()->user()->name }}</h1>
            <p class="mt-1 text-sm text-slate-500">System-wide health, revenue and pending actions.</p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('admin.ai.edit') }}"     class="btn-secondary !py-2 text-xs">🤖 AI settings</a>
            <a href="{{ route('admin.settings') }}"    class="btn-secondary !py-2 text-xs">⚙️ Platform settings</a>
            <a href="{{ route('admin.homepage') }}"    class="btn-primary !py-2 text-xs">🏠 Edit homepage</a>
        </div>
    </div>

    {{-- Missing tables warning --}}
    @if(! empty($missingTables))
        <div class="mt-6 flex items-start gap-3 rounded-2xl border border-amber-300 bg-amber-50 p-4 text-sm text-amber-900">
            <span class="grid h-9 w-9 shrink-0 place-items-center rounded-xl bg-gradient-to-br from-amber-500 to-orange-500 text-white shadow-sm">⚠</span>
            <div class="flex-1">
                <p class="font-bold">Some migrations haven't been run yet.</p>
                <p class="mt-1 text-xs text-amber-800">
                    Missing: <code class="rounded bg-white/70 px-1 py-0.5">{{ implode(', ', $missingTables) }}</code>.
                    Run <code class="rounded bg-white/70 px-1.5 py-0.5">php artisan migrate</code> from the project root to enable the corresponding features.
                </p>
            </div>
        </div>
    @endif

    {{-- ═══════════════════════════ HERO KPI BAND ═══════════════════════════ --}}
    @php
        $gmvInr    = '₹'.number_format($stats['gmv_cents'] / 100, 2, '.', ',');
        $escrowInr = '₹'.number_format($stats['escrow_held'] / 100, 2, '.', ',');
    @endphp
    <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        {{-- Paid GMV --}}
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-emerald-500 via-teal-500 to-cyan-500 p-5 text-white shadow-lg">
            <div class="pointer-events-none absolute -right-6 -top-6 h-24 w-24 rounded-full bg-white/20 blur-2xl"></div>
            <div class="flex items-center justify-between">
                <p class="text-[10px] font-bold uppercase tracking-widest opacity-90">Paid GMV</p>
                <span class="text-lg">💰</span>
            </div>
            <p class="mt-2 text-3xl font-black leading-none">{{ $gmvInr }}</p>
            <p class="mt-1 text-[11px] opacity-90">All-time creator payouts released</p>
        </div>

        {{-- Escrow held --}}
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-violet-500 via-purple-500 to-pink-500 p-5 text-white shadow-lg">
            <div class="pointer-events-none absolute -right-6 -top-6 h-24 w-24 rounded-full bg-white/20 blur-2xl"></div>
            <div class="flex items-center justify-between">
                <p class="text-[10px] font-bold uppercase tracking-widest opacity-90">Escrow held</p>
                <span class="text-lg">🔒</span>
            </div>
            <p class="mt-2 text-3xl font-black leading-none">{{ $escrowInr }}</p>
            <a href="{{ route('admin.escrow.index') }}" class="mt-1 inline-block text-[11px] opacity-90 hover:underline">Manage escrow →</a>
        </div>

        {{-- Active campaigns --}}
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-amber-500 via-orange-500 to-rose-500 p-5 text-white shadow-lg">
            <div class="pointer-events-none absolute -right-6 -top-6 h-24 w-24 rounded-full bg-white/20 blur-2xl"></div>
            <div class="flex items-center justify-between">
                <p class="text-[10px] font-bold uppercase tracking-widest opacity-90">Campaigns</p>
                <span class="text-lg">🚀</span>
            </div>
            <p class="mt-2 text-3xl font-black leading-none">{{ number_format($stats['campaigns']) }}</p>
            <p class="mt-1 text-[11px] opacity-90">Across all workspaces</p>
        </div>

        {{-- New leads --}}
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-sky-500 via-blue-500 to-indigo-500 p-5 text-white shadow-lg">
            <div class="pointer-events-none absolute -right-6 -top-6 h-24 w-24 rounded-full bg-white/20 blur-2xl"></div>
            <div class="flex items-center justify-between">
                <p class="text-[10px] font-bold uppercase tracking-widest opacity-90">New leads</p>
                <span class="text-lg">📥</span>
            </div>
            <p class="mt-2 text-3xl font-black leading-none">{{ number_format($stats['leads_new']) }}</p>
            <a href="{{ route('admin.leads.index') }}" class="mt-1 inline-block text-[11px] opacity-90 hover:underline">Review leads →</a>
        </div>
    </div>

    {{-- ═══════════════════════════ SECONDARY STATS (mini) ═══════════════════════════ --}}
    <div class="mt-4 grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-6">
        @php
            $mini = [
                ['label' => 'Users',        'value' => number_format($stats['users']),        'icon' => '👥', 'accent' => 'text-violet-600',  'href' => route('admin.users.index')],
                ['label' => 'Creators',     'value' => number_format($stats['creators']),     'icon' => '🎬', 'accent' => 'text-rose-600',    'href' => route('admin.creators.index')],
                ['label' => 'Workspaces',   'value' => number_format($stats['workspaces']),   'icon' => '🏢', 'accent' => 'text-sky-600',     'href' => route('admin.workspaces.index')],
                ['label' => 'Applications', 'value' => number_format($stats['applications']), 'icon' => '📋', 'accent' => 'text-emerald-600', 'href' => route('admin.dashboard')],
                ['label' => 'Blog posts',   'value' => number_format($stats['blog_posts']),   'icon' => '📝', 'accent' => 'text-amber-600',   'href' => route('admin.blog.index')],
                ['label' => 'SEO pages',    'value' => '132',                                  'icon' => '🔍', 'accent' => 'text-slate-600',   'href' => route('admin.seo')],
            ];
        @endphp
        @foreach($mini as $m)
            <a href="{{ $m['href'] }}" class="group card p-4 transition hover:border-violet-300 hover:shadow-md">
                <div class="flex items-center justify-between">
                    <p class="text-[10px] font-bold uppercase tracking-widest text-slate-500">{{ $m['label'] }}</p>
                    <span class="text-base opacity-70 group-hover:opacity-100">{{ $m['icon'] }}</span>
                </div>
                <p class="mt-1 text-2xl font-black tracking-tight text-slate-900">{{ $m['value'] }}</p>
            </a>
        @endforeach
    </div>

    {{-- ═══════════════════════════ MAIN CONTENT GRID ═══════════════════════════ --}}
    <div class="mt-8 grid gap-6 lg:grid-cols-3">
        {{-- Latest leads (spans 2 cols) --}}
        <section class="lg:col-span-2 rounded-2xl border border-slate-200 bg-white p-5 md:p-6">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-bold text-slate-900">📥 Latest leads</h2>
                <a href="{{ route('admin.leads.index') }}" class="text-xs font-semibold text-violet-700 hover:underline">View all →</a>
            </div>
            <div class="mt-4 space-y-2">
                @forelse($recentLeads as $l)
                    <a href="{{ route('admin.leads.index') }}" class="flex items-center gap-3 rounded-xl border border-slate-100 p-3 transition hover:border-violet-300 hover:bg-violet-50/30">
                        <div class="grid h-10 w-10 place-items-center rounded-full bg-gradient-to-br from-violet-500 to-pink-500 font-bold text-white text-sm shadow-sm">{{ strtoupper(substr($l->name, 0, 1)) }}</div>
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-semibold text-slate-900">{{ $l->name }} <span class="text-xs font-normal text-slate-500">· {{ $l->email }}</span></p>
                            <p class="truncate text-xs text-slate-500">{{ $l->company ?: '—' }} · {{ ucfirst($l->reason ?? '') }} · {{ $l->created_at->diffForHumans() }}</p>
                        </div>
                        <x-badge :tone="match($l->status) { 'new' => 'amber', 'contacted' => 'sky', 'qualified' => 'sky', 'won' => 'green', 'lost' => 'rose', default => 'slate' }">{{ $l->status }}</x-badge>
                    </a>
                @empty
                    <div class="rounded-xl border border-dashed border-slate-200 bg-slate-50/50 p-6 text-center">
                        <p class="text-2xl">📭</p>
                        <p class="mt-2 text-sm text-slate-500">No leads yet — waiting on your first contact-form submit.</p>
                    </div>
                @endforelse
            </div>
        </section>

        {{-- Quick actions --}}
        <aside class="rounded-2xl border border-slate-200 bg-gradient-to-br from-slate-900 to-slate-950 p-5 text-white md:p-6">
            <h2 class="text-lg font-bold">⚡ Quick actions</h2>
            <p class="mt-1 text-xs text-slate-400">Common admin tasks, one click away.</p>
            <div class="mt-5 space-y-2">
                <a href="{{ route('admin.creators.import') }}" class="flex items-center justify-between rounded-xl bg-white/5 px-4 py-3 text-sm font-semibold transition hover:bg-white/10">
                    <span>📤 Import creators (CSV)</span><span class="text-slate-400">→</span>
                </a>
                <a href="{{ route('admin.blog.create') }}" class="flex items-center justify-between rounded-xl bg-white/5 px-4 py-3 text-sm font-semibold transition hover:bg-white/10">
                    <span>✍️ New blog post</span><span class="text-slate-400">→</span>
                </a>
                <a href="{{ route('admin.homepage') }}" class="flex items-center justify-between rounded-xl bg-white/5 px-4 py-3 text-sm font-semibold transition hover:bg-white/10">
                    <span>🏠 Manage homepage</span><span class="text-slate-400">→</span>
                </a>
                <a href="{{ route('admin.ai.edit') }}" class="flex items-center justify-between rounded-xl bg-gradient-to-r from-violet-500/20 to-pink-500/20 px-4 py-3 text-sm font-semibold transition hover:from-violet-500/30 hover:to-pink-500/30">
                    <span>🤖 AI provider &amp; key</span><span class="text-slate-400">→</span>
                </a>
                <a href="{{ route('admin.integrations.edit') }}" class="flex items-center justify-between rounded-xl bg-gradient-to-r from-emerald-500/20 to-cyan-500/20 px-4 py-3 text-sm font-semibold transition hover:from-emerald-500/30 hover:to-cyan-500/30">
                    <span>🔌 Mail · Razorpay · Analytics</span><span class="text-slate-400">→</span>
                </a>
                <a href="{{ route('admin.seo') }}" class="flex items-center justify-between rounded-xl bg-white/5 px-4 py-3 text-sm font-semibold transition hover:bg-white/10">
                    <span>🔍 SEO dashboard</span><span class="text-slate-400">→</span>
                </a>
            </div>
        </aside>
    </div>

    {{-- ═══════════════════════════ RECENT ACTIVITY (users + creators) ═══════════════════════════ --}}
    <div class="mt-6 grid gap-6 lg:grid-cols-2">
        <section class="rounded-2xl border border-slate-200 bg-white p-5 md:p-6">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-bold text-slate-900">👥 Recent users</h2>
                <a href="{{ route('admin.users.index') }}" class="text-xs font-semibold text-violet-700 hover:underline">View all →</a>
            </div>
            <div class="mt-4 space-y-2">
                @forelse($recentUsers as $u)
                    <a href="{{ route('admin.users.show', $u) }}" class="flex items-center gap-3 rounded-xl border border-slate-100 p-3 transition hover:border-violet-300 hover:bg-violet-50/30">
                        <div class="grid h-9 w-9 shrink-0 place-items-center rounded-full bg-gradient-to-br from-slate-700 to-slate-900 font-bold text-white text-sm">{{ strtoupper(substr($u->name,0,1)) }}</div>
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-semibold text-slate-900">{{ $u->name }}</p>
                            <p class="truncate text-xs text-slate-500">{{ $u->email }} · Joined {{ $u->created_at->diffForHumans() }}</p>
                        </div>
                        @if(method_exists($u, 'isSuperAdmin') && $u->isSuperAdmin())<x-badge tone="violet">Superadmin</x-badge>
                        @elseif(method_exists($u, 'isAdmin') && $u->isAdmin())<x-badge tone="violet">Admin</x-badge>
                        @elseif(($u->account_status ?? null) === 'suspended')<x-badge tone="rose">Suspended</x-badge>
                        @else<x-badge tone="slate">User</x-badge>
                        @endif
                    </a>
                @empty
                    <p class="text-sm text-slate-500">No users yet.</p>
                @endforelse
            </div>
        </section>

        <section class="rounded-2xl border border-slate-200 bg-white p-5 md:p-6">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-bold text-slate-900">🎬 Recent creators</h2>
                <a href="{{ route('admin.creators.index') }}" class="text-xs font-semibold text-violet-700 hover:underline">View all →</a>
            </div>
            <div class="mt-4 space-y-2">
                @forelse($recentCreators as $c)
                    <a href="{{ route('admin.creators.show', $c) }}" class="flex items-center gap-3 rounded-xl border border-slate-100 p-3 transition hover:border-violet-300 hover:bg-violet-50/30">
                        <div class="grid h-9 w-9 shrink-0 place-items-center rounded-full bg-gradient-to-br from-rose-500 to-pink-500 font-bold text-white text-sm">{{ strtoupper(substr($c->display_name,0,1)) }}</div>
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-semibold text-slate-900">{{ $c->display_name }}</p>
                            <p class="truncate text-xs text-slate-500">{{ number_format($c->follower_count_total) }} followers · {{ $c->engagement_rate }}% ER{{ $c->city ? ' · '.$c->city : '' }}</p>
                        </div>
                        <x-badge :tone="$c->status === 'active' ? 'green' : ($c->status === 'suspended' ? 'rose' : 'slate')">{{ $c->status }}</x-badge>
                    </a>
                @empty
                    <p class="text-sm text-slate-500">No creators yet.</p>
                @endforelse
            </div>
        </section>
    </div>
</x-layouts.admin>
