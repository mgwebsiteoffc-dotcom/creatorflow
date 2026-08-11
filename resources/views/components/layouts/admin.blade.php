@props(['title' => null])
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex,nofollow">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <title>{{ $title ? $title.' · ' : '' }}Admin · CreatorFlow</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-100 text-slate-900">

<div class="flex min-h-screen">
    {{-- Sidebar --}}
    <aside class="sticky top-0 hidden h-screen w-60 shrink-0 flex-col gap-1 border-r border-slate-200 bg-slate-950 p-3 text-slate-200 md:flex">
        <a href="{{ route('admin.dashboard') }}" class="mb-3 flex items-center gap-2 rounded-xl bg-white/5 p-3">
            <span class="grid h-8 w-8 place-items-center rounded-lg text-xs font-black text-white" style="background-image: linear-gradient(135deg,#7c3aed,#ec4899 60%,#f59e0b);">CF</span>
            <div>
                <div class="text-sm font-bold text-white">CreatorFlow</div>
                <div class="text-[10px] uppercase tracking-widest text-slate-400">Admin</div>
            </div>
        </a>

        @php
            $items = [
                ['route' => 'admin.dashboard',      'label' => 'Dashboard',   'icon' => '🏠'],
                ['route' => 'admin.users.index',    'label' => 'Users',       'icon' => '👥'],
                ['route' => 'admin.creators.index', 'label' => 'Creators',    'icon' => '🎬'],
                ['route' => 'admin.workspaces.index','label' => 'Workspaces', 'icon' => '🏢'],
                ['route' => 'admin.leads.index',    'label' => 'Leads',       'icon' => '📥'],
                ['route' => 'admin.escrow.index',   'label' => 'Escrow',      'icon' => '🔒'],
                ['route' => 'admin.homepage',       'label' => 'Homepage',    'icon' => '🏠'],
                ['route' => 'admin.blog.index',     'label' => 'Blog',        'icon' => '📝'],
                ['route' => 'admin.seo',            'label' => 'SEO',         'icon' => '🔍'],
                ['route' => 'admin.settings',       'label' => 'Settings',    'icon' => '⚙️'],
            ];
        @endphp
        @foreach($items as $it)
            <a href="{{ route($it['route']) }}"
               class="flex items-center gap-2 rounded-lg px-3 py-2 text-sm font-medium transition
                      {{ request()->routeIs(str_replace('.index','.*', $it['route'])) || request()->routeIs($it['route']) ? 'bg-white/10 text-white' : 'text-slate-300 hover:bg-white/5 hover:text-white' }}">
                <span>{{ $it['icon'] }}</span> {{ $it['label'] }}
            </a>
        @endforeach

        <div class="mt-auto space-y-1 border-t border-white/10 pt-3">
            <a href="{{ url('/') }}" class="block rounded-lg px-3 py-2 text-xs text-slate-400 hover:bg-white/5 hover:text-white">↩ Back to site</a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="w-full rounded-lg px-3 py-2 text-left text-xs text-slate-400 hover:bg-white/5 hover:text-white">Sign out</button>
            </form>
        </div>
    </aside>

    {{-- Main --}}
    <main class="min-w-0 flex-1">
        <header class="sticky top-0 z-30 border-b border-slate-200 bg-white/85 backdrop-blur md:hidden">
            <div class="flex h-14 items-center justify-between px-4">
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2 font-bold">
                    <span class="grid h-8 w-8 place-items-center rounded-lg text-xs font-black text-white" style="background-image: linear-gradient(135deg,#7c3aed,#ec4899 60%,#f59e0b);">CF</span>
                    Admin
                </a>
                <a href="{{ url('/') }}" class="text-xs font-semibold text-slate-500">↩ Site</a>
            </div>
        </header>

        <div class="mx-auto w-full max-w-6xl px-4 py-8 md:px-8 md:py-10">
            @if(session('status'))
                <div class="mb-6 flex items-center justify-between gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
                    <span>✓ {{ session('status') }}</span>
                </div>
            @endif
            @if(session('error'))
                <div class="mb-6 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800">{{ session('error') }}</div>
            @endif
            @if($errors->any())
                <div class="mb-6 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800">
                    <ul class="list-disc pl-4">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                </div>
            @endif

            {{ $slot }}
        </div>
    </main>
</div>

</body>
</html>
