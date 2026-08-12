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
                    ['route' => 'creator.dashboard', 'label' => 'Home', 'icon' => 'home'],
                ],
            ],
            [
                'label' => 'Discover',
                'items' => [
                    ['route' => 'creator.marketplace',  'label' => 'Marketplace',  'icon' => 'marketplace'],
                    ['route' => 'creator.applications', 'label' => 'Applications', 'icon' => 'applications'],
                    ['route' => 'creator.invitations',  'label' => 'Invitations',  'icon' => 'invitations'],
                ],
            ],
            [
                'label' => 'Work',
                'items' => [
                    ['route' => 'creator.assignments.index', 'label' => 'My Work',   'icon' => 'work'],
                    ['route' => 'creator.earnings.index',    'label' => 'Earnings',  'icon' => 'earnings'],
                ],
            ],
            [
                'label' => 'Communication',
                'items' => [
                    ['route' => 'messages.index',       'label' => 'Messages',      'icon' => 'messages'],
                    ['route' => 'notifications.index',  'label' => 'Notifications', 'icon' => 'notifications'],
                ],
            ],
            [
                'label' => 'Profile',
                'items' => [
                    ['route' => 'creator.profile.show', 'label' => 'Public profile', 'icon' => 'profile'],
                    ['route' => 'creator.profile.edit', 'label' => 'Edit profile',   'icon' => 'edit'],
                    ['route' => 'creator.payout.edit',  'label' => 'Payout',         'icon' => 'payout'],
                ],
            ],
        ];
        $logoGrad = 'linear-gradient(135deg,#f43f5e,#ec4899 60%,#f59e0b)';
        $panelLabel = 'Creator';
    } else {
        $groups = [
            [
                'label' => 'Overview',
                'items' => [
                    ['route' => 'brand.dashboard', 'label' => 'Home', 'icon' => 'home'],
                ],
            ],
            [
                'label' => 'Campaigns',
                'items' => [
                    ['route' => 'brand.campaigns.index',    'label' => 'Campaigns',    'icon' => 'campaigns'],
                    ['route' => 'brand.applications.index', 'label' => 'Applications', 'icon' => 'applications'],
                    ['route' => 'brand.creators.index',     'label' => 'Creators',     'icon' => 'creators'],
                    ['route' => 'brand.assignments.index',  'label' => 'Assignments',  'icon' => 'assignments'],
                ],
            ],
            [
                'label' => 'Fulfillment',
                'items' => [
                    ['route' => 'brand.orders.index',    'label' => 'Orders',   'icon' => 'orders'],
                    ['route' => 'brand.products.index',  'label' => 'Products', 'icon' => 'products'],
                    ['route' => 'brand.channels.index',  'label' => 'Channels', 'icon' => 'channels'],
                ],
            ],
            [
                'label' => 'Insights',
                'items' => [
                    ['route' => 'brand.analytics', 'label' => 'Analytics', 'icon' => 'analytics'],
                ],
            ],
            [
                'label' => 'Communication',
                'items' => [
                    ['route' => 'messages.index',       'label' => 'Messages',      'icon' => 'messages'],
                    ['route' => 'notifications.index',  'label' => 'Notifications', 'icon' => 'notifications'],
                ],
            ],
            [
                'label' => 'Account',
                'items' => [
                    ['route' => 'brand.billing.index',     'label' => 'Billing',   'icon' => 'billing'],
                    ['route' => 'brand.settings.team',     'label' => 'Team',      'icon' => 'team'],
                    ['route' => 'brand.settings.profile',  'label' => 'Settings',  'icon' => 'settings'],
                ],
            ],
        ];
        $logoGrad = 'linear-gradient(135deg,#7c3aed,#ec4899 60%,#f59e0b)';
        $panelLabel = 'Brand';
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
            <x-icon name="close" class="h-5 w-5" />
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
                        @php
                            $active = $isActive($item['route']);
                            $badge  = null;
                            if ($item['route'] === 'notifications.index' && ($unreadCount ?? 0) > 0) {
                                $badge = ((int) $unreadCount) > 9 ? '9+' : (string) $unreadCount;
                            }
                        @endphp
                        <a href="{{ route($item['route']) }}"
                           class="group relative flex items-center gap-2.5 rounded-lg px-3 py-2 text-sm font-medium transition
                                  {{ $active
                                      ? 'bg-slate-900 text-white shadow-sm'
                                      : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                            <x-icon :name="$item['icon']"
                                    class="h-[18px] w-[18px] shrink-0 {{ $active ? 'text-white' : 'text-slate-500 group-hover:text-slate-900' }}" />
                            <span class="min-w-0 flex-1 truncate">{{ $item['label'] }}</span>
                            @if($badge)
                                <span class="inline-flex h-4 min-w-4 shrink-0 items-center justify-center rounded-full bg-rose-500 px-1 text-[10px] font-bold text-white">{{ $badge }}</span>
                            @elseif($active)
                                <span class="h-1.5 w-1.5 shrink-0 rounded-full bg-gradient-to-r from-violet-400 to-pink-400"></span>
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
                   class="flex items-center gap-2.5 rounded-lg px-3 py-2 text-xs font-semibold text-violet-700 hover:bg-violet-50">
                    <x-icon name="admin" class="h-[18px] w-[18px]" />Admin panel
                </a>
            @endif
        @endauth
        <a href="{{ url('/') }}" class="flex items-center gap-2.5 rounded-lg px-3 py-2 text-xs text-slate-500 hover:bg-slate-50 hover:text-slate-900">
            <x-icon name="external" class="h-[18px] w-[18px]" />Back to site
        </a>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="w-full text-left flex items-center gap-2.5 rounded-lg px-3 py-2 text-xs text-slate-500 hover:bg-slate-50 hover:text-rose-600">
                <x-icon name="logout" class="h-[18px] w-[18px]" />Sign out
            </button>
        </form>
    </div>
</aside>
