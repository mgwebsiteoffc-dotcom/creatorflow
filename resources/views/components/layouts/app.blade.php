@props([
    'panel' => 'brand', // brand | creator | guest
    'title' => null,
    'metaDescription' => null,
    'canonical' => null,
    'ogImage' => null,
])
@php
    // Guest pages get a soft playful gradient (violet → pink → amber wash) so
    // the marketing site never feels like a plain white sheet.
    $panelClass = $panel === 'creator'
        ? 'bg-rose-50'
        : ($panel === 'guest' ? 'guest-bg' : 'bg-slate-50');
    $workspace = $currentWorkspace ?? null;
    $creator = $currentCreator ?? auth()->user()?->creator;
    $isGuest = $panel === 'guest';
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#7c5cff">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="CreatorPlex">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="manifest" href="/manifest.webmanifest">
    <link rel="apple-touch-icon" href="/icons/icon-192.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <title>{{ $title ? $title.' · ' : '' }}CreatorPlex — The AI creator-commerce platform</title>
    @php
        $descText = trim((string) ($metaDescription ?? 'CreatorPlex is the AI-powered creator-commerce platform. Launch campaigns in minutes, seed products in bulk, and attribute revenue — for Shopify or any brand.'));
        $canonicalUrl = $canonical ?? url()->current();
    @endphp
    <meta name="description" content="{{ $descText }}">
    <link rel="canonical" href="{{ $canonicalUrl }}">
    <meta property="og:type" content="website">
    <meta property="og:title" content="{{ $title ?: 'CreatorPlex' }}">
    <meta property="og:description" content="{{ $descText }}">
    <meta property="og:url" content="{{ $canonicalUrl }}">
    @if($ogImage)<meta property="og:image" content="{{ $ogImage }}">@endif
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $title ?: 'CreatorPlex' }}">
    <meta name="twitter:description" content="{{ $descText }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @include('partials.analytics-head')
</head>
<body class="min-h-screen {{ $panelClass }} text-slate-900 antialiased">

    @if($isGuest)
        {{-- Global playful background: soft aurora blobs + subtle grid + drifting SVG shapes.
             Fixed so it stays in view as you scroll. Everything is pointer-events-none. --}}
        <div class="guest-bg-layer" aria-hidden="true">
            <span class="blob blob-1"></span>
            <span class="blob blob-2"></span>
            <span class="blob blob-3"></span>
            <span class="blob blob-4"></span>

            <svg class="floater floater-a" viewBox="0 0 60 60" fill="none">
                <defs><linearGradient id="fla" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="#7c3aed"/><stop offset="1" stop-color="#ec4899"/></linearGradient></defs>
                <circle cx="30" cy="30" r="24" stroke="url(#fla)" stroke-width="2" opacity=".55"/>
                <circle cx="30" cy="30" r="10" fill="url(#fla)" opacity=".18"/>
            </svg>
            <svg class="floater floater-b" viewBox="0 0 60 60" fill="none">
                <defs><linearGradient id="flb" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="#f59e0b"/><stop offset="1" stop-color="#ec4899"/></linearGradient></defs>
                <polygon points="30,4 56,52 4,52" stroke="url(#flb)" stroke-width="2" fill="none" opacity=".55"/>
            </svg>
            <svg class="floater floater-c" viewBox="0 0 60 60" fill="none">
                <defs><linearGradient id="flc" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="#06b6d4"/><stop offset="1" stop-color="#8b5cf6"/></linearGradient></defs>
                <path d="M6 30 Q18 6 30 30 T54 30" stroke="url(#flc)" stroke-width="3" fill="none" opacity=".55" stroke-linecap="round"/>
            </svg>
            <svg class="floater floater-d" viewBox="0 0 60 60" fill="none">
                <defs><linearGradient id="fld" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="#10b981"/><stop offset="1" stop-color="#0ea5e9"/></linearGradient></defs>
                <rect x="8" y="8" width="44" height="44" rx="10" stroke="url(#fld)" stroke-width="2" opacity=".5"/>
            </svg>
        </div>
    @endif

    @if($isGuest)
        @include('partials.marketing-nav')

        <div class="w-full" style="overflow-x: clip;">
            @if(session('status'))
                <x-flash type="success">{{ session('status') }}</x-flash>
            @endif
            @if(session('error'))
                <x-flash type="error">{{ session('error') }}</x-flash>
            @endif
            @if($errors->any())
                <x-flash type="error">
                    <ul class="list-disc pl-4">
                        @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                    </ul>
                </x-flash>
            @endif

            {{ $slot }}
        </div>
    @else
        {{-- App panels (brand / creator) get a grouped sidebar + slim header --}}
        <div class="flex min-h-screen">
            {{-- Sidebar drawer backdrop (mobile only) --}}
            <div data-sidebar-backdrop class="fixed inset-0 z-30 hidden bg-slate-900/60 backdrop-blur-sm md:hidden"></div>

            @include('partials.sidebar', ['panel' => $panel, 'workspace' => $workspace, 'creator' => $creator])

            <main class="flex min-w-0 flex-1 flex-col">
                @include('partials.app-header', ['panel' => $panel, 'workspace' => $workspace])

                <div class="mx-auto w-full max-w-6xl flex-1 px-4 pb-28 pt-6 md:px-6 md:pb-12 md:pt-8 lg:px-8" style="overflow-x: clip;">
                    @if(session('status'))
                        <x-flash type="success">{{ session('status') }}</x-flash>
                    @endif
                    @if(session('error'))
                        <x-flash type="error">{{ session('error') }}</x-flash>
                    @endif
                    @if($errors->any())
                        <x-flash type="error">
                            <ul class="list-disc pl-4">
                                @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                            </ul>
                        </x-flash>
                    @endif

                    {{ $slot }}
                </div>
            </main>
        </div>
    @endif

    @if($isGuest)
        @include('partials.marketing-footer')
    @else
        @include('partials.bottomnav', ['panel' => $panel])
    @endif

    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => navigator.serviceWorker.register('/sw.js').catch(() => {}));
        }
    </script>
    @include('partials.pwa-install')
    @include('partials.analytics-body')
</body>
</html>
