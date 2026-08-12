@php
    if ($panel === 'creator') {
        $items = [
            ['route' => 'creator.dashboard', 'icon' => 'M3 12l9-9 9 9M5 10v10h5v-6h4v6h5V10', 'label' => 'Home'],
            ['route' => 'creator.marketplace', 'icon' => 'M4 6h16M4 12h16M4 18h16', 'label' => 'Market'],
            ['route' => 'creator.applications', 'icon' => 'M9 12l2 2 4-4M12 3a9 9 0 100 18 9 9 0 000-18z', 'label' => 'Applied'],
            ['route' => 'creator.assignments.index', 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4', 'label' => 'Work'],
            ['route' => 'creator.profile.show', 'icon' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z', 'label' => 'Profile'],
        ];
    } else {
        $items = [
            ['route' => 'brand.dashboard', 'icon' => 'M3 12l9-9 9 9M5 10v10h5v-6h4v6h5V10', 'label' => 'Home'],
            ['route' => 'brand.campaigns.index', 'icon' => 'M11 5.88V19m0-13.12a3 3 0 00-3.7-2.8L4.6 4.2A2 2 0 003.3 6.1L3 17.9a2 2 0 002.6 2l2.5-.6a3 3 0 003-2.8M11 5.88A3 3 0 0114.7 3l2.7.6a2 2 0 011.6 1.9L19 19a2 2 0 01-1.6 2l-2.5-.6a3 3 0 01-3.9-2.6', 'label' => 'Campaigns'],
            ['route' => 'brand.creators.index', 'icon' => 'M17 20h5v-2a4 4 0 00-3-3.9M9 20H4v-2a4 4 0 013-3.9m6 1.9a4 4 0 10-6-5.5M13 7a3 3 0 11-6 0 3 3 0 016 0z', 'label' => 'Creators'],
            ['route' => 'brand.orders.index', 'icon' => 'M3 5h2l1.4 8.3a2 2 0 002 1.7h8.2a2 2 0 002-1.7L20 7H6M9 20a1 1 0 100-2 1 1 0 000 2zm9 0a1 1 0 100-2 1 1 0 000 2z', 'label' => 'Orders'],
            ['label' => 'More', 'icon' => 'M4 6h16M4 12h16M4 18h16', 'more' =>true],
        ];
    }
@endphp

<nav class="fixed inset-x-0 bottom-0 z-30 border-t border-slate-200 bg-white/95 backdrop-blur md:hidden"
     style="padding-bottom: env(safe-area-inset-bottom)">
    <div class="mx-auto grid max-w-6xl grid-cols-5">
        @foreach($items as $item)
            @if($item['more'] ?? false)
                <button type="button" data-bottomnav-more
                        class="flex flex-col items-center gap-0.5 py-2.5 text-[11px] font-medium text-slate-500">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="{{ $item['icon'] }}"/>
                    </svg>
                    {{ $item['label'] }}
                </button>
            @else
                <a href="{{ route($item['route']) }}"
                   class="flex flex-col items-center gap-0.5 py-2.5 text-[11px] font-medium
                   {{ request()->routeIs($item['route']) ? 'text-violet-600' : 'text-slate-500' }}">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="{{ $item['icon'] }}"/>
                    </svg>
                    {{ $item['label'] }}
                </a>
            @endif
        @endforeach
    </div>
</nav>

@if($panel === 'brand')
    {{-- Slide-up 'More' sheet with the overflow menu items --}}
    <div data-bottomnav-more-sheet class="fixed inset-0 z-40 hidden md:hidden">
        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" data-bottomnav-close></div>
        <div class="absolute inset-x-0 bottom-0 rounded-t-2xl bg-white p-4 pb-8 shadow-2xl"
             style="padding-bottom: calc(env(safe-area-inset-bottom) + 1rem)">
            <div class="mx-auto mb-4 h-1.5 w-10 rounded-full bg-slate-200"></div>
            <p class="mb-3 text-center text-xs font-bold uppercase tracking-widest text-slate-500">More</p>
            <div class="grid grid-cols-4 gap-3">
                @foreach([
                    ['brand.applications.index', '', 'Applications'],
                    ['brand.products.index', '', 'Products'],
                    ['brand.channels.index', '', 'Channels'],
                    ['brand.analytics', '', 'Analytics'],
                    ['brand.billing.index', '', 'Billing'],
                    ['brand.settings.profile', '', 'Settings'],
                    ['brand.settings.team', '', 'Team'],
                    ['notifications.index', '', 'Alerts'],
                ] as $it)
                    <a href="{{ route($it[0]) }}"
                       class="flex flex-col items-center gap-1.5 rounded-xl border border-slate-100 bg-slate-50 p-3 text-center text-[11px] font-semibold text-slate-700 hover:border-violet-200 hover:bg-violet-50">
                        <span class="text-2xl">{{ $it[1] }}</span>
                        {{ $it[2] }}
                    </a>
                @endforeach
            </div>
        </div>
    </div>
    <script>
        document.querySelectorAll('[data-bottomnav-more]').forEach(btn => {
            const sheet = document.querySelector('[data-bottomnav-more-sheet]');
            btn.addEventListener('click', () =>sheet?.classList.toggle('hidden'));
        });
        document.querySelectorAll('[data-bottomnav-close]').forEach(el => {
            el.addEventListener('click', () =>el.closest('[data-bottomnav-more-sheet]')?.classList.add('hidden'));
        });
    </script>
@endif
