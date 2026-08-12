@php
    // Core 5 shown as top-nav pills; everything else in a "More" dropdown so the
    // topbar never wraps to a second line no matter the viewport width.
    $brandLinks = [
        ['route' => 'brand.dashboard',          'label' => 'Home'],
        ['route' => 'brand.campaigns.index',    'label' => 'Campaigns'],
        ['route' => 'brand.creators.index',     'label' => 'Creators'],
        ['route' => 'brand.orders.index',       'label' => 'Orders'],
        ['route' => 'brand.analytics',          'label' => 'Analytics'],
    ];
    $brandMoreLinks = [
        ['route' => 'brand.applications.index', 'label' => 'Applications', 'icon' => '📥'],
        ['route' => 'brand.products.index',     'label' => 'Products',     'icon' => '📦'],
        ['route' => 'brand.channels.index',     'label' => 'Channels',     'icon' => '🛍'],
        ['route' => 'brand.billing.index',      'label' => 'Billing',      'icon' => '💳'],
        ['route' => 'brand.settings.profile',   'label' => 'Settings',     'icon' => '⚙️'],
        ['route' => 'brand.settings.team',      'label' => 'Team',         'icon' => '👥'],
        ['route' => 'notifications.index',      'label' => 'Notifications','icon' => '🔔'],
    ];
    $creatorLinks = [
        ['route' => 'creator.dashboard', 'label' => 'Home'],
        ['route' => 'creator.marketplace', 'label' => 'Marketplace'],
        ['route' => 'creator.applications', 'label' => 'Applications'],
        ['route' => 'creator.assignments.index', 'label' => 'My Work'],
        ['route' => 'creator.earnings.index', 'label' => 'Earnings'],
        ['route' => 'creator.profile.show', 'label' => 'Profile'],
    ];
    $links = $panel === 'creator' ? $creatorLinks : $brandLinks;
    $logoGrad = $panel === 'creator'
        ? 'linear-gradient(135deg,#f43f5e,#ec4899 60%,#f59e0b)'
        : 'linear-gradient(135deg,#7c3aed,#ec4899 60%,#f59e0b)';
@endphp

<header class="sticky top-0 z-30 border-b border-slate-200 bg-white/85 backdrop-blur">
    <div class="mx-auto flex h-16 w-full max-w-6xl items-center justify-between gap-3 px-4 md:px-6 lg:px-8">
        <a href="{{ url('/') }}" class="flex items-center gap-2 font-bold">
            <span class="grid h-8 w-8 place-items-center rounded-xl text-xs font-black text-white shadow-sm"
                  style="background-image: {{ $logoGrad }};">CP</span>
            <span class="hidden text-slate-900 sm:block">CreatorPlex</span>
            <span class="badge-violet {{ $panel === 'creator' ? '!bg-rose-100 !text-rose-700' : '' }}">{{ ucfirst($panel) }}</span>
        </a>

        <nav class="hidden min-w-0 flex-1 items-center gap-1 md:flex md:justify-center">
            @foreach($links as $link)
                <a href="{{ route($link['route']) }}"
                   class="whitespace-nowrap rounded-lg px-3 py-1.5 text-sm font-medium text-slate-600 hover:bg-slate-100
                   {{ request()->routeIs(str_replace('index','*', $link['route'])) ? 'bg-slate-100 text-slate-900' : '' }}">
                    {{ $link['label'] }}
                </a>
            @endforeach

            @if($panel !== 'creator' && ! empty($brandMoreLinks ?? []))
                {{-- "More" dropdown for the overflow items so the top row never wraps --}}
                <div class="relative" data-more-wrap>
                    <button type="button" data-more-toggle
                            class="whitespace-nowrap rounded-lg px-3 py-1.5 text-sm font-medium text-slate-600 hover:bg-slate-100 flex items-center gap-1">
                        More
                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" d="M6 9l6 6 6-6"/></svg>
                    </button>
                    <div data-more-panel class="absolute right-0 top-full z-40 mt-2 hidden w-56 overflow-hidden rounded-xl border border-slate-200 bg-white shadow-2xl">
                        @foreach($brandMoreLinks as $link)
                            <a href="{{ route($link['route']) }}"
                               class="flex items-center gap-2 px-3 py-2 text-sm text-slate-700 hover:bg-violet-50 hover:text-violet-900
                               {{ request()->routeIs(str_replace('index','*', $link['route'])) ? 'bg-violet-50 text-violet-800 font-semibold' : '' }}">
                                <span class="text-base">{{ $link['icon'] }}</span> {{ $link['label'] }}
                            </a>
                        @endforeach
                    </div>
                </div>
                <script>
                    (function(){
                        const wrap = document.querySelector('[data-more-wrap]');
                        if (!wrap) return;
                        const btn  = wrap.querySelector('[data-more-toggle]');
                        const panel= wrap.querySelector('[data-more-panel]');
                        btn.addEventListener('click', e => { e.stopPropagation(); panel.classList.toggle('hidden'); });
                        document.addEventListener('click', e => { if (!wrap.contains(e.target)) panel.classList.add('hidden'); });
                    })();
                </script>
            @endif
        </nav>

        <div class="flex items-center gap-2">
            <button id="pwa-install" class="btn-secondary hidden !py-1.5 !text-xs">Install app</button>

            {{-- Notifications bell --}}
            <div class="relative" data-notif-wrap>
                <button type="button" data-notif-toggle class="btn-ghost relative !p-2" title="Notifications">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.4-1.4A2 2 0 0118 14.2V11a6 6 0 10-12 0v3.2c0 .5-.2 1-.6 1.4L4 17h5m6 0a3 3 0 11-6 0"/></svg>
                    @if(($unreadCount ?? 0) > 0)
                        <span class="absolute -right-0.5 -top-0.5 grid h-4 min-w-4 place-items-center rounded-full bg-rose-500 px-1 text-[10px] font-bold text-white">{{ min(9, $unreadCount) }}{{ $unreadCount > 9 ? '+' : '' }}</span>
                    @endif
                </button>
                <div data-notif-panel class="absolute right-0 top-full z-50 mt-2 hidden w-80 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-xl">
                    <div class="flex items-center justify-between border-b border-slate-100 px-4 py-3">
                        <div>
                            <p class="text-sm font-bold text-slate-900">Notifications</p>
                            <p class="text-[11px] text-slate-500">{{ $unreadCount ?? 0 }} unread</p>
                        </div>
                        <a href="{{ route('notifications.index') }}" class="text-xs font-semibold text-violet-700 hover:text-violet-900">See all →</a>
                    </div>
                    <div class="max-h-96 overflow-y-auto">
                        @forelse(($recentNotifications ?? []) as $n)
                            <a href="{{ route('notifications.open', $n) }}"
                               class="flex items-start gap-2 border-b border-slate-100 px-4 py-3 last:border-0
                                      {{ $n->read_at ? '' : 'bg-violet-50/40' }} hover:bg-slate-50">
                                <span class="mt-0.5 text-lg">{{ str_contains($n->type, 'approved') ? '✅' : (str_contains($n->type, 'content') ? '🎬' : '🔔') }}</span>
                                <div class="min-w-0 flex-1">
                                    <p class="truncate text-sm font-semibold text-slate-900">{{ $n->data['title'] ?? '' }}</p>
                                    @if($n->data['body'] ?? null)<p class="line-clamp-2 text-xs text-slate-500">{{ $n->data['body'] }}</p>@endif
                                    <p class="mt-0.5 text-[11px] text-slate-400">{{ $n->created_at->diffForHumans() }}</p>
                                </div>
                                @if(! $n->read_at)<span class="mt-2 h-2 w-2 shrink-0 rounded-full bg-violet-600"></span>@endif
                            </a>
                        @empty
                            <div class="px-4 py-8 text-center text-xs text-slate-500">You're all caught up 🎉</div>
                        @endforelse
                    </div>
                </div>
            </div>

            <a href="{{ route('messages.index') }}" class="btn-ghost !p-2" title="Messages">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 10h.01M12 10h.01M16 10h.01M21 12a8 8 0 01-11.5 7.2L3 21l1.8-6.5A8 8 0 1121 12z"/></svg>
            </a>
            @if($panel === 'brand' && $workspace)
                <span class="hidden max-w-[140px] truncate rounded-lg bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-600 sm:inline-block">{{ $workspace->name }}</span>
            @endif
            @if($panel === 'brand')
                <a href="{{ route('brand.settings.profile') }}" class="btn-ghost !p-2" title="Settings">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </a>
            @endif
            @auth
                @if(auth()->user()->isAdmin())
                    <a href="{{ route('admin.dashboard') }}" class="btn-secondary !py-1.5 !text-xs" title="Admin panel">🛡️ Admin</a>
                @endif
            @endauth
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="btn-secondary !py-1.5 !text-xs">Sign out</button>
            </form>
        </div>
    </div>
</header>
