@php
    if ($panel === 'creator') {
        $items = [
            ['route' => 'creator.dashboard', 'icon' => 'M3 12l9-9 9 9M5 10v10h5v-6h4v6h5V10', 'label' => 'Home'],
            ['route' => 'creator.marketplace', 'icon' => 'M4 6h16M4 12h16M4 18h16', 'label' => 'Market'],
            ['route' => 'creator.assignments.index', 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4', 'label' => 'Work'],
            ['route' => 'creator.earnings.index', 'icon' => 'M12 8c-1.7 0-3 .9-3 2s1.3 2 3 2 3 .9 3 2-1.3 2-3 2m0-8c1.1 0 2.1.4 2.8 1M12 8V7m0 1v1m0 6v1m0-1c-1.1 0-2.1-.4-2.8-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z', 'label' => 'Pay'],
            ['route' => 'creator.profile.show', 'icon' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z', 'label' => 'Profile'],
        ];
    } else {
        $items = [
            ['route' => 'brand.dashboard', 'icon' => 'M3 12l9-9 9 9M5 10v10h5v-6h4v6h5V10', 'label' => 'Home'],
            ['route' => 'brand.campaigns.index', 'icon' => 'M11 5.88V19m0-13.12a3 3 0 00-3.7-2.8L4.6 4.2A2 2 0 003.3 6.1L3 17.9a2 2 0 002.6 2l2.5-.6a3 3 0 003-2.8M11 5.88A3 3 0 0114.7 3l2.7.6a2 2 0 011.6 1.9L19 19a2 2 0 01-1.6 2l-2.5-.6a3 3 0 01-3.9-2.6', 'label' => 'Campaigns'],
            ['route' => 'brand.products.index', 'icon' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-14L4 7m8 4v10M4 7v10l8 4', 'label' => 'Products'],
            ['route' => 'brand.creators.index', 'icon' => 'M17 20h5v-2a4 4 0 00-3-3.9M9 20H4v-2a4 4 0 013-3.9m6 1.9a4 4 0 10-6-5.5M13 7a3 3 0 11-6 0 3 3 0 016 0z', 'label' => 'Creators'],
            ['route' => 'brand.analytics', 'icon' => 'M3 3v18h18M7 15l4-4 3 3 5-6', 'label' => 'Analytics'],
        ];
    }
@endphp

<nav class="fixed inset-x-0 bottom-0 z-30 border-t border-slate-200 bg-white/95 backdrop-blur md:hidden"
     style="padding-bottom: env(safe-area-inset-bottom)">
    <div class="mx-auto grid max-w-6xl grid-cols-5">
        @foreach($items as $item)
            <a href="{{ route($item['route']) }}"
               class="flex flex-col items-center gap-0.5 py-2.5 text-[11px] font-medium
               {{ request()->routeIs($item['route']) ? 'text-violet-600' : 'text-slate-500' }}">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="{{ $item['icon'] }}"/>
                </svg>
                {{ $item['label'] }}
            </a>
        @endforeach
    </div>
</nav>
