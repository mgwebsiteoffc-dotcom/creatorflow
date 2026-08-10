@php
    $brandLinks = [
        ['route' => 'brand.dashboard', 'label' => 'Home'],
        ['route' => 'brand.campaigns.index', 'label' => 'Campaigns'],
        ['route' => 'brand.products.index', 'label' => 'Products'],
        ['route' => 'brand.creators.index', 'label' => 'Creators'],
        ['route' => 'brand.assignments.index', 'label' => 'Assignments'],
        ['route' => 'brand.analytics', 'label' => 'Analytics'],
    ];
    $creatorLinks = [
        ['route' => 'creator.dashboard', 'label' => 'Home'],
        ['route' => 'creator.marketplace', 'label' => 'Marketplace'],
        ['route' => 'creator.assignments.index', 'label' => 'My Work'],
        ['route' => 'creator.earnings.index', 'label' => 'Earnings'],
        ['route' => 'creator.profile.show', 'label' => 'Profile'],
    ];
    $links = $panel === 'creator' ? $creatorLinks : $brandLinks;
    $primary = $panel === 'creator' ? 'bg-rose-600' : 'bg-violet-600';
@endphp

<header class="sticky top-0 z-30 border-b border-slate-200 bg-white/90 backdrop-blur">
    <div class="mx-auto flex h-14 w-full max-w-6xl items-center justify-between gap-3 px-4">
        <a href="{{ url('/') }}" class="flex items-center gap-2 font-bold">
            <span class="grid h-8 w-8 place-items-center rounded-xl {{ $primary }} text-white">CF</span>
            <span class="hidden sm:block">CreatorFlow</span>
            <span class="badge {{ $panel === 'creator' ? 'badge-rose' : 'badge-violet' }}">{{ ucfirst($panel) }}</span>
        </a>

        <nav class="hidden items-center gap-1 md:flex">
            @foreach($links as $link)
                <a href="{{ route($link['route']) }}"
                   class="rounded-lg px-3 py-1.5 text-sm font-medium text-slate-600 hover:bg-slate-100
                   {{ request()->routeIs(str_replace('index','*', $link['route'])) ? 'bg-slate-100 text-slate-900' : '' }}">
                    {{ $link['label'] }}
                </a>
            @endforeach
        </nav>

        <div class="flex items-center gap-2">
            <button id="pwa-install" class="btn-secondary hidden !py-1.5 text-xs">Install app</button>
            <a href="{{ route('messages.index') }}" class="btn-ghost !px-2.5" title="Messages">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 10h.01M12 10h.01M16 10h.01M21 12a8 8 0 01-11.5 7.2L3 21l1.8-6.5A8 8 0 1121 12z"/></svg>
            </a>
            @if($panel === 'brand' && $workspace)
                <span class="hidden text-xs text-slate-500 sm:block">{{ $workspace->name }}</span>
            @endif
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="btn-secondary !py-1.5 text-xs">Sign out</button>
            </form>
        </div>
    </div>
</header>
