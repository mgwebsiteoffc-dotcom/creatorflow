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
    <title>{{ $title ? $title.' · ' : '' }}Admin · CreatorPlex</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @include('partials.analytics-head')
</head>
<body class="min-h-screen bg-slate-100 text-slate-900" style="overflow-x: clip;">

<div class="flex min-h-screen">
    {{-- Sidebar --}}
    <aside class="sticky top-0 hidden h-screen w-60 shrink-0 flex-col gap-1 border-r border-slate-200 bg-slate-950 p-3 text-slate-200 md:flex">
        <a href="{{ route('admin.dashboard') }}" class="mb-3 flex items-center gap-2 rounded-xl bg-white/5 p-3">
            <span class="grid h-8 w-8 place-items-center rounded-lg text-xs font-black text-white" style="background-image: linear-gradient(135deg,#7c3aed,#ec4899 60%,#f59e0b);">CP</span>
            <div>
                <div class="text-sm font-bold text-white">CreatorPlex</div>
                <div class="text-[10px] uppercase tracking-widest text-slate-400">Admin</div>
            </div>
        </a>

        @php
            $items = [
                ['route' => 'admin.dashboard',      'label' => 'Dashboard',   'icon' => 'dashboard'],
                ['route' => 'admin.users.index',    'label' => 'Users',       'icon' => 'users'],
                ['route' => 'admin.creators.index', 'label' => 'Creators',    'icon' => 'creators'],
                ['route' => 'admin.workspaces.index','label' => 'Workspaces', 'icon' => 'workspaces'],
                ...(\App\Models\PlatformSetting::feature('agency_mode') ? [
                    ['route' => 'admin.agencies.index', 'label' => 'Agencies', 'icon' => 'agencies'],
                ] : []),
                ['route' => 'admin.leads.index',    'label' => 'Leads',       'icon' => 'leads'],
                ['route' => 'admin.billing.index',  'label' => 'Billing',     'icon' => 'billing'],
                ['route' => 'admin.escrow.index',   'label' => 'Escrow',      'icon' => 'escrow'],
                ['route' => 'admin.homepage',       'label' => 'Homepage',    'icon' => 'homepage'],
                ['route' => 'admin.blog.index',     'label' => 'Blog',        'icon' => 'blog'],
                ...(\App\Models\PlatformSetting::feature('case_study_cms') ? [
                    ['route' => 'admin.case-studies.index', 'label' => 'Case studies', 'icon' => 'case-studies'],
                ] : []),
                ['route' => 'admin.seo',            'label' => 'SEO',         'icon' => 'seo'],
                ...(\App\Models\PlatformSetting::feature('ab_testing') ? [
                    ['route' => 'admin.ab.index', 'label' => 'A/B tests', 'icon' => 'ab-test'],
                ] : []),
                ...(\App\Models\PlatformSetting::feature('referrals') ? [
                    ['route' => 'admin.referrals.index', 'label' => 'Referrals', 'icon' => 'referrals'],
                ] : []),
                ['route' => 'admin.ai.edit',           'label' => 'AI keys',      'icon' => 'ai'],
                ['route' => 'admin.integrations.edit',         'label' => 'Integrations',  'icon' => 'integrations'],
                ['route' => 'admin.notification-templates.index','label' => 'Templates',    'icon' => 'templates'],
                ['route' => 'admin.settings',                  'label' => 'Settings',       'icon' => 'settings'],
            ];
        @endphp
        @foreach($items as $it)
            @php $active = request()->routeIs(str_replace('.index','.*', $it['route'])) || request()->routeIs($it['route']); @endphp
            <a href="{{ route($it['route']) }}"
               class="group flex items-center gap-2.5 rounded-lg px-3 py-2 text-sm font-medium transition
                      {{ $active ? 'bg-white/10 text-white' : 'text-slate-300 hover:bg-white/5 hover:text-white' }}">
                <x-icon :name="$it['icon']"
                        class="h-[18px] w-[18px] shrink-0 {{ $active ? 'text-white' : 'text-slate-400 group-hover:text-white' }}" />
                {{ $it['label'] }}
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
                    <span class="grid h-8 w-8 place-items-center rounded-lg text-xs font-black text-white" style="background-image: linear-gradient(135deg,#7c3aed,#ec4899 60%,#f59e0b);">CP</span>
                    Admin
                </a>
                <a href="{{ url('/') }}" class="text-xs font-semibold text-slate-500">↩ Site</a>
            </div>
        </header>

        <div class="mx-auto w-full max-w-7xl px-4 py-8 md:px-8 md:py-10">
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
