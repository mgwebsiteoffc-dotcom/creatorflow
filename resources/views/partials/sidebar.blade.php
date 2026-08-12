@php
    /** @var string $panel  'brand'|'creator' */
    $panel = $panel ?? 'brand';
    $workspace = $workspace ?? ($currentWorkspace ?? null);
    $creator   = $creator   ?? (auth()->user()?->creator);

    if ($panel === 'creator') {
        $groups = [
            [
                'label' => 'Overview',
                'items' => [
                    ['route' => 'creator.dashboard', 'label' => 'Home', 'icon' => '🏠'],
                ],
            ],
            [
                'label' => 'Discover',
                'items' => [
                    ['route' => 'creator.marketplace',  'label' => 'Marketplace',  'icon' => '🛍'],
                    ['route' => 'creator.applications', 'label' => 'Applications', 'icon' => '📥'],
                    ['route' => 'creator.invitations',  'label' => 'Invitations',  'icon' => '✉️'],
                ],
            ],
            [
                'label' => 'Work',
                'items' => [
                    ['route' => 'creator.assignments.index', 'label' => 'My Work',   'icon' => '📋'],
                    ['route' => 'creator.earnings.index',    'label' => 'Earnings',  'icon' => '💰'],
                ],
            ],
            [
                'label' => 'Communication',
                'items' => [
                    ['route' => 'messages.index',       'label' => 'Messages',      'icon' => '💬'],
                    ['route' => 'notifications.index',  'label' => 'Notifications', 'icon' => '🔔'],
                ],
            ],
            [
                'label' => 'Profile',
                'items' => [
                    ['route' => 'creator.profile.show', 'label' => 'Public profile', 'icon' => '👤'],
                    ['route' => 'creator.profile.edit', 'label' => 'Edit profile',   'icon' => '✏️'],
                    ['route' => 'creator.payout.edit',  'label' => 'Payout',         'icon' => '🏦'],
                ],
            ],
        ];
        $logoGrad = 'linear-gradient(135deg,#f43f5e,#ec4899 60%,#f59e0b)';
        $panelLabel = 'Creator';
        $badgeClass = '!bg-rose-100 !text-rose-700';
    } else {
        $groups = [
            [
                'label' => 'Overview',
                'items' => [
                    ['route' => 'brand.dashboard', 'label' => 'Home', 'icon' => '🏠'],
                ],
            ],
            [
                'label' => 'Campaigns',
                'items' => [
                    ['route' => 'brand.campaigns.index',    'label' => 'Campaigns',    'icon' => '🚀'],
                    ['route' => 'brand.applications.index', 'label' => 'Applications', 'icon' => '📥'],
                    ['route' => 'brand.creators.index',     'label' => 'Creators',     'icon' => '🎬'],
                    ['route' => 'brand.assignments.index',  'label' => 'Assignments',  'icon' => '📋'],
                ],
            ],
            [
                'label' => 'Fulfillment',
                'items' => [
                    ['route' => 'brand.orders.index',    'label' => 'Orders',   'icon' => '📦'],
                    ['route' => 'brand.products.index',  'label' => 'Products', 'icon' => '🎁'],
                    ['route' => 'brand.channels.index',  'label' => 'Channels', 'icon' => '🛍'],
                ],
            ],
            [
                'label' => 'Insights',
                'items' => [
                    ['route' => 'brand.analytics', 'label' => 'Analytics', 'icon' => '📊'],
                ],
            ],
            [
                'label' => 'Communication',
                'items' => [
                    ['route' => 'messages.index',       'label' => 'Messages',      'icon' => '💬'],
                    ['route' => 'notifications.index',  'label' => 'Notifications', 'icon' => '🔔'],
                ],
            ],
            [
                'label' => 'Account',
                'items' => [
                    ['route' => 'brand.billing.index',     'label' => 'Billing',   'icon' => '💳'],
                    ['route' => 'brand.settings.team',     'label' => 'Team',      'icon' => '👥'],
                    ['route' => 'brand.settings.profile',  'label' => 'Settings',  'icon' => '⚙️'],
                ],
            ],
        ];
        $logoGrad = 'linear-gradient(135deg,#7c3aed,#ec4899 60%,#f59e0b)';
        $panelLabel = 'Brand';
        $badgeClass = '';
    }

    /** True if the current request matches one of the item route names. */
    $isActive = function (string $routeName): bool {
        if (request()->routeIs($routeName)) return true;
        if (str_ends_with($routeName, '.index')) {
            return request()->routeIs(str_replace('.index', '.*', $routeName));
        }
        return false;
    };
@endphp

<aside data-sidebar
       class="fixed inset-y-0 left-0 z-40 hidden w-64 shrink-0 flex-col border-r border-slate-200 bg-white md:sticky md:top-0 md:flex md:h-screen">

    {{-- Brand block --}}
    <a href="{{ route($panel === 'creator' ? 'creator.dashboard' : 'brand.dashboard') }}"
       class="flex items-center gap-2.5 border-b border-slate-100 px-4 py-4">
        <span class="grid h-9 w-9 place-items-center rounded-xl text-xs font-black text-white shadow-sm"
              style="background-image: {{ $logoGrad }};">CP</span>
        <div class="min-w-0 flex-1">
            <div class="truncate text-sm font-bold text-slate-900">CreatorPlex</div>
            <div class="text-[10px] font-bold uppercase tracking-widest text-slate-400">{{ $panelLabel }}</div>
        </div>
        <button type="button" data-sidebar-close class="rounded-lg p-1.5 text-slate-400 hover:bg-slate-100 md:hidden" aria-label="Close menu">
            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" d="M6 6l12 12M6 18L18 6"/></svg>
        </button>
    </a>

    {{-- Workspace / creator switcher --}}
    @if($panel === 'brand' && $workspace)
        <div class="border-b border-slate-100 px-4 py-3">
            <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Workspace</p>
            <p class="mt-1 truncate text-sm font-semibold text-slate-900">{{ $workspace->name }}</p>
        </div>
    @elseif($panel === 'creator' && $creator)
        <div class="border-b border-slate-100 px-4 py-3">
            <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Creator</p>
            <p class="mt-1 truncate text-sm font-semibold text-slate-900">{{ $creator->display_name }}</p>
        </div>
    @endif

    {{-- Grouped nav --}}
    <nav class="flex-1 overflow-y-auto px-2 py-3">
        @foreach($groups as $group)
            <div class="mb-4">
                <p class="px-3 pb-1.5 text-[10px] font-bold uppercase tracking-widest text-slate-400">{{ $group['label'] }}</p>
                <div class="space-y-0.5">
                    @foreach($group['items'] as $item)
                        @php $active = $isActive($item['route']); @endphp
                        <a href="{{ route($item['route']) }}"
                           class="group flex items-center gap-2.5 rounded-lg px-3 py-2 text-sm font-medium transition
                                  {{ $active
                                      ? 'bg-gradient-to-r from-violet-50 to-pink-50 text-violet-900'
                                      : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                            <span class="grid h-6 w-6 place-items-center text-base {{ $active ? '' : 'opacity-80 group-hover:opacity-100' }}">{{ $item['icon'] }}</span>
                            <span class="min-w-0 flex-1 truncate">{{ $item['label'] }}</span>
                            @if($active)
                                <span class="h-1.5 w-1.5 rounded-full bg-gradient-to-r from-violet-500 to-pink-500"></span>
                            @endif
                        </a>
                    @endforeach
                </div>
            </div>
        @endforeach
    </nav>

    {{-- Bottom actions --}}
    <div class="border-t border-slate-100 p-2">
        @auth
            @if(auth()->user()->isAdmin())
                <a href="{{ route('admin.dashboard') }}"
                   class="flex items-center gap-2 rounded-lg px-3 py-2 text-xs font-semibold text-violet-700 hover:bg-violet-50">
                    🛡️ Admin panel
                </a>
            @endif
        @endauth
        <a href="{{ url('/') }}" class="flex items-center gap-2 rounded-lg px-3 py-2 text-xs text-slate-500 hover:bg-slate-50 hover:text-slate-900">↩ Back to site</a>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="w-full text-left flex items-center gap-2 rounded-lg px-3 py-2 text-xs text-slate-500 hover:bg-slate-50 hover:text-rose-600">
                <span>🚪</span> Sign out
            </button>
        </form>
    </div>
</aside>
