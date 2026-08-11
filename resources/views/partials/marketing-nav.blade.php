<header class="sticky top-0 z-40 glass-nav">
    <div class="mx-auto flex h-16 w-full max-w-6xl items-center justify-between gap-3 px-4">
        <a href="{{ url('/') }}" class="flex items-center gap-2 font-bold">
            <span class="relative grid h-9 w-9 place-items-center rounded-xl text-white"
                  style="background-image: linear-gradient(135deg,#7c3aed,#ec4899 60%,#f59e0b);">
                <span class="text-sm font-black">CF</span>
                <span class="absolute -right-1 -top-1 h-2.5 w-2.5 rounded-full bg-cyan-400 ring-2 ring-white"></span>
            </span>
            <span class="text-slate-900">CreatorFlow</span>
        </a>

        <nav id="nav-menu" class="absolute left-0 right-0 top-16 hidden flex-col gap-1 border-b border-slate-200 bg-white/95 p-4 backdrop-blur md:static md:flex md:flex-row md:items-center md:gap-1 md:border-0 md:bg-transparent md:p-0">
            <a href="#platform" class="rounded-lg px-3 py-2 text-sm font-medium text-slate-600 hover:bg-slate-100">Platform</a>
            <a href="#campaigns" class="rounded-lg px-3 py-2 text-sm font-medium text-slate-600 hover:bg-slate-100">Campaigns</a>
            <a href="#why" class="rounded-lg px-3 py-2 text-sm font-medium text-slate-600 hover:bg-slate-100">Why us</a>
            <a href="#creators" class="rounded-lg px-3 py-2 text-sm font-medium text-slate-600 hover:bg-slate-100">Creators</a>
            <a href="#pricing" class="rounded-lg px-3 py-2 text-sm font-medium text-slate-600 hover:bg-slate-100">Pricing</a>
        </nav>

        <div class="flex items-center gap-2">
            @auth
                <a href="{{ auth()->user()->creator ? route('creator.dashboard') : route('brand.dashboard') }}" class="btn-gradient !py-2 !text-xs">Dashboard →</a>
            @else
                <a href="{{ route('login') }}" class="hidden text-sm font-medium text-slate-600 hover:text-slate-900 sm:inline">Sign in</a>
                <a href="{{ route('register') }}" class="btn-gradient !py-2 !text-xs">Start free</a>
            @endauth
            <button id="nav-toggle" class="btn-ghost !px-2 md:hidden" aria-label="Toggle menu">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>
        </div>
    </div>
</header>
