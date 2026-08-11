@props([
    'panel' => 'brand', // brand | creator | guest
    'title' => null,
    'metaDescription' => null,
    'canonical' => null,
    'ogImage' => null,
])
@php
    $panelClass = $panel === 'creator' ? 'bg-rose-50' : ($panel === 'guest' ? 'bg-white' : 'bg-slate-50');
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
    <meta name="apple-mobile-web-app-title" content="CreatorFlow">
    <link rel="manifest" href="/manifest.webmanifest">
    <link rel="apple-touch-icon" href="/icons/icon-192.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <title>{{ $title ? $title.' · ' : '' }}CreatorFlow — The AI creator-commerce platform</title>
    @php
        $descText = trim((string) ($metaDescription ?? 'CreatorFlow is the AI-powered creator-commerce platform. Launch campaigns in minutes, seed products in bulk, and attribute revenue — for Shopify or any brand.'));
        $canonicalUrl = $canonical ?? url()->current();
    @endphp
    <meta name="description" content="{{ $descText }}">
    <link rel="canonical" href="{{ $canonicalUrl }}">
    <meta property="og:type" content="website">
    <meta property="og:title" content="{{ $title ?: 'CreatorFlow' }}">
    <meta property="og:description" content="{{ $descText }}">
    <meta property="og:url" content="{{ $canonicalUrl }}">
    @if($ogImage)<meta property="og:image" content="{{ $ogImage }}">@endif
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $title ?: 'CreatorFlow' }}">
    <meta name="twitter:description" content="{{ $descText }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen {{ $panelClass }} text-slate-900">

    @if($isGuest)
        @include('partials.marketing-nav')
    @else
        @include('partials.topbar', compact('panel', 'workspace', 'creator'))
    @endif

    <div class="{{ $isGuest ? 'w-full' : 'mx-auto w-full max-w-6xl px-4 pb-28 pt-6 md:px-6 md:pb-16 md:pt-10 lg:px-8' }}">
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
</body>
</html>
