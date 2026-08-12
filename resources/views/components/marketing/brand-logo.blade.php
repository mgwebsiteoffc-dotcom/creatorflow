@props(['name', 'url' =>null, 'link' =>null, 'size' => 'md'])
@php
    // Deterministic gradient per brand — same colours every time.
    $palettes = [
        ['#7c3aed','#ec4899'],  // violet → pink
        ['#06b6d4','#10b981'],  // cyan → emerald
        ['#f59e0b','#f43f5e'],  // amber → rose
        ['#6366f1','#8b5cf6'],  // indigo → violet
        ['#10b981','#0d9488'],  // emerald → teal
        ['#ec4899','#f97316'],  // pink → orange
        ['#0ea5e9','#6366f1'],  // sky → indigo
        ['#eab308','#ea580c'],  // yellow → orange
        ['#a855f7','#3b82f6'],  // purple → blue
        ['#f43f5e','#a855f7'],  // rose → purple
        ['#14b8a6','#22c55e'],  // teal → green
        ['#8b5cf6','#ec4899'],  // violet → pink
    ];
    $shapes = ['circle','square','diamond','hex','star','wave'];

    $seed = abs(crc32($name));
    $palette = $palettes[$seed % count($palettes)];
    $shape   = $shapes[($seed >>3) % count($shapes)];
    $rot     = ($seed % 24) - 12; // small tilt for personality

    // Extract 1–2 letter initials from the brand name.
    $initial = collect(preg_split('/\s+/', trim($name)))
        ->filter()
        ->take(2)
        ->map(fn ($w) =>mb_strtoupper(mb_substr($w, 0, 1)))
        ->implode('');
    if ($initial === '') $initial = 'CF';

    $textSize = match ($size) {
        'sm' => 'text-lg', 'lg' => 'text-2xl', default => 'text-xl',
    };
    $iconWH = match ($size) {
        'sm' => 'h-8 w-8', 'lg' => 'h-12 w-12', default => 'h-10 w-10',
    };
    $gradId = 'brand-grad-'.substr(md5($name), 0, 8);
@endphp
<div class="flex items-center gap-2.5">
    <span class="relative grid {{ $iconWH }} place-items-center overflow-hidden shrink-0 shadow-sm"
          style="border-radius: {{ $shape === 'circle' ? '9999px' : ($shape === 'diamond' ? '.5rem' : ($shape === 'hex' ? '25%' : ($shape === 'wave' ? '35% 65% 65% 35% / 45% 45% 55% 55%' : '.75rem'))) }}; transform: rotate({{ $rot }}deg);">
        <svg viewBox="0 0 40 40" class="absolute inset-0 h-full w-full" aria-hidden="true">
            <defs>
                <linearGradient id="{{ $gradId }}" x1="0" y1="0" x2="1" y2="1">
                    <stop offset="0%" stop-color="{{ $palette[0] }}"/>
                    <stop offset="100%" stop-color="{{ $palette[1] }}"/>
                </linearGradient>
            </defs>
            <rect x="0" y="0" width="40" height="40" fill="url(#{{ $gradId }})"/>
            {{-- shape-specific decoration --}}
            @if($shape === 'star')
                <path d="M20 6l4.2 8.6 9.5 1.3-6.9 6.7 1.7 9.4L20 27.5l-8.5 4.5 1.7-9.4-6.9-6.7 9.5-1.3z" fill="rgba(255,255,255,.28)"/>
            @elseif($shape === 'wave')
                <path d="M0 26 Q10 20 20 26 T40 26 V40 H0z" fill="rgba(255,255,255,.22)"/>
            @elseif($shape === 'hex')
                <polygon points="20,4 34,12 34,28 20,36 6,28 6,12" fill="rgba(255,255,255,.15)"/>
            @else
                <circle cx="28" cy="12" r="6" fill="rgba(255,255,255,.22)"/>
                <circle cx="10" cy="30" r="3" fill="rgba(255,255,255,.15)"/>
            @endif
        </svg>
        @if($url)
            <img src="{{ $url }}" alt="{{ $name }}" class="relative z-10 max-h-full max-w-full object-contain">
        @else
            <span class="relative z-10 font-black text-white" style="transform: rotate({{ -$rot }}deg);">{{ $initial }}</span>
        @endif
    </span>
    <span class="whitespace-nowrap font-black tracking-tight {{ $textSize }} text-slate-700">{{ $name }}</span>
</div>
