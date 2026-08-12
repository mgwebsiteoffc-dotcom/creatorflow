@props([
    'name' => 'home',
    'class' => 'h-5 w-5',
    'stroke' => '1.75',
])

@php
    // Monoline SVG icon library (Lucide-inspired, hand-tuned for CreatorPlex).
    // Every icon renders on a 24x24 grid, uses currentColor + round joins,
    // and is optical-weight matched so they read cleanly at 20px in the sidebar.
    $paths = [
        'home'          => '<path d="M3 11.5 12 4l9 7.5"/><path d="M5 10v10a1 1 0 0 0 1 1h4v-6h4v6h4a1 1 0 0 0 1-1V10"/>',
        'campaigns'     => '<path d="m3 11 18-6-4 15-6-4-4 5-1-8-3-2Z"/>',
        'applications'  => '<path d="M4 5h16v14H4z"/><path d="M4 9h16"/><path d="M8 13h4"/><path d="M8 16h6"/>',
        'creators'      => '<circle cx="9" cy="9" r="3.2"/><path d="M3 20c.7-3.2 3-5 6-5s5.3 1.8 6 5"/><circle cx="17" cy="7.5" r="2.2"/><path d="M15.5 14.5c2.4.2 4.2 1.7 4.8 4"/>',
        'assignments'   => '<rect x="5" y="4" width="14" height="17" rx="2"/><path d="M9 4v2h6V4"/><path d="M8.5 11l2 2 4-4"/><path d="M8.5 17h5.5"/>',
        'orders'        => '<path d="m3 7 9-4 9 4-9 4-9-4Z"/><path d="m3 12 9 4 9-4"/><path d="m3 17 9 4 9-4"/>',
        'products'      => '<rect x="4" y="7" width="16" height="13" rx="2"/><path d="M4 11h16"/><path d="M9 4h6a2 2 0 0 1 2 2v1H7V6a2 2 0 0 1 2-2Z"/>',
        'channels'      => '<path d="M4 6h13a3 3 0 0 1 0 6H8"/><path d="m8 15 4-3-4-3"/><path d="M4 12v6"/>',
        'analytics'     => '<path d="M4 20V10"/><path d="M10 20V4"/><path d="M16 20v-7"/><path d="M22 20H2"/>',
        'messages'      => '<path d="M4 12a8 8 0 0 1 16 0c0 4-3.6 7-8 7-1 0-2-.2-3-.5L4 20l1.5-4.5A7 7 0 0 1 4 12Z"/>',
        'notifications' => '<path d="M6 9a6 6 0 0 1 12 0v4l1.5 3H4.5L6 13V9Z"/><path d="M10 19a2 2 0 0 0 4 0"/>',
        'billing'       => '<rect x="3" y="6" width="18" height="13" rx="2"/><path d="M3 10h18"/><path d="M7 15h4"/>',
        'team'          => '<circle cx="9" cy="9" r="3"/><path d="M3 20c.5-3.5 3-5 6-5s5.5 1.5 6 5"/><circle cx="17" cy="7.5" r="2"/><path d="M16 15c2.4.1 4.2 1.6 5 5"/>',
        'settings'      => '<circle cx="12" cy="12" r="3"/><path d="M19 12a7 7 0 0 0-.1-1.3l2-1.5-2-3.4-2.3.9a7 7 0 0 0-2.2-1.3L14 3h-4l-.4 2.4a7 7 0 0 0-2.2 1.3l-2.3-.9-2 3.4 2 1.5A7 7 0 0 0 5 12c0 .4 0 .9.1 1.3l-2 1.5 2 3.4 2.3-.9a7 7 0 0 0 2.2 1.3L10 21h4l.4-2.4a7 7 0 0 0 2.2-1.3l2.3.9 2-3.4-2-1.5c.1-.4.1-.9.1-1.3Z"/>',
        // Discover / creator side
        'marketplace'   => '<path d="M4 8h16l-1 11a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2L4 8Z"/><path d="M8 8V6a4 4 0 0 1 8 0v2"/>',
        'invitations'   => '<rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3.5 6.5 8.5 6 8.5-6"/>',
        'work'          => '<rect x="3" y="7" width="18" height="13" rx="2"/><path d="M9 7V5a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v2"/><path d="M3 13h18"/>',
        'earnings'      => '<circle cx="12" cy="12" r="9"/><path d="M9 8h6M9 12h6M11 8v9M13 8v9"/>',
        'profile'       => '<circle cx="12" cy="8.5" r="3.5"/><path d="M4.5 20c.8-4 4-6 7.5-6s6.7 2 7.5 6"/>',
        'edit'          => '<path d="M4 20h4l10-10-4-4L4 16v4Z"/><path d="m14 6 4 4"/>',
        'payout'        => '<path d="M4 8h16v11H4z"/><path d="M4 8V6a2 2 0 0 1 2-2h9l5 4"/><circle cx="16" cy="14" r="2"/>',
        // Utility
        'admin'         => '<path d="M12 3 4 6v6c0 5 3.5 8 8 9 4.5-1 8-4 8-9V6l-8-3Z"/><path d="m9 12 2 2 4-4"/>',
        'external'      => '<path d="M14 4h6v6"/><path d="M20 4 10 14"/><path d="M20 14v4a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h4"/>',
        'logout'        => '<path d="M14 8V6a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h7a2 2 0 0 0 2-2v-2"/><path d="M18 15l3-3-3-3"/><path d="M21 12H9"/>',
        'close'         => '<path d="M6 6l12 12M6 18 18 6"/>',
        'menu'          => '<path d="M4 6h16M4 12h16M4 18h16"/>',
        'chevron-down'  => '<path d="m6 9 6 6 6-6"/>',
        'search'        => '<circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/>',
        'bell'          => '<path d="M6 9a6 6 0 0 1 12 0v4l1.5 3H4.5L6 13V9Z"/><path d="M10 19a2 2 0 0 0 4 0"/>',
        // Admin extras
        'dashboard'     => '<rect x="3" y="3" width="7" height="9" rx="1.5"/><rect x="14" y="3" width="7" height="5" rx="1.5"/><rect x="14" y="12" width="7" height="9" rx="1.5"/><rect x="3" y="16" width="7" height="5" rx="1.5"/>',
        'users'         => '<circle cx="9" cy="9" r="3.2"/><path d="M3 20c.7-3.2 3-5 6-5s5.3 1.8 6 5"/><circle cx="17" cy="7.5" r="2.2"/><path d="M15.5 14.5c2.4.2 4.2 1.7 4.8 4"/>',
        'workspaces'    => '<rect x="4" y="4" width="16" height="16" rx="2"/><path d="M4 10h16"/><path d="M10 4v16"/>',
        'agencies'      => '<path d="M4 21V8l8-4 8 4v13"/><path d="M9 21v-7h6v7"/><path d="M4 21h16"/>',
        'leads'         => '<path d="M4 4h16v4H4z"/><path d="M4 12h16v4H4z"/><path d="M4 20h10"/>',
        'escrow'        => '<rect x="4" y="10" width="16" height="10" rx="2"/><path d="M8 10V7a4 4 0 0 1 8 0v3"/><circle cx="12" cy="15" r="1.5"/>',
        'homepage'      => '<path d="M3 11.5 12 4l9 7.5"/><path d="M5 10v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V10"/><path d="M9 21v-6h6v6"/>',
        'blog'          => '<path d="M5 4h9l5 5v11a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V5a1 1 0 0 1 1-1Z"/><path d="M14 4v5h5"/><path d="M8 13h8M8 17h6"/>',
        'case-studies'  => '<rect x="4" y="4" width="16" height="16" rx="2"/><path d="M4 9h16"/><path d="M8 13h8M8 17h5"/>',
        'seo'           => '<circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/><path d="M8 11h6M11 8v6"/>',
        'ab-test'       => '<path d="M10 4h4l5 16h-3l-1-4h-6l-1 4H5L10 4Z"/><path d="M9.5 12h5"/>',
        'referrals'     => '<path d="M20 12v9H4v-9"/><path d="M2 7h20v5H2z"/><path d="M12 22V7"/><path d="M12 7C9 7 6 5 6 3s3-1 6 4c3-5 6-5 6-3s-3 4-6 4Z"/>',
        'ai'            => '<circle cx="12" cy="12" r="9"/><path d="M8 12c0-2 1.5-4 4-4s4 2 4 4-1.5 4-4 4-4-2-4-4Z"/><path d="M12 3v3M12 18v3M3 12h3M18 12h3"/>',
        'integrations'  => '<rect x="4" y="4" width="7" height="7" rx="1.5"/><rect x="13" y="4" width="7" height="7" rx="1.5"/><rect x="4" y="13" width="7" height="7" rx="1.5"/><rect x="13" y="13" width="7" height="7" rx="1.5"/>',
        'templates'     => '<rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3.5 6.5 8.5 6 8.5-6"/>',
        'shield'        => '<path d="M12 3 4 6v6c0 5 3.5 8 8 9 4.5-1 8-4 8-9V6l-8-3Z"/>',
    ];

    $body = $paths[$name] ?? $paths['home'];
@endphp

<svg xmlns="http://www.w3.org/2000/svg"
     viewBox="0 0 24 24"
     fill="none"
     stroke="currentColor"
     stroke-width="{{ $stroke }}"
     stroke-linecap="round"
     stroke-linejoin="round"
     aria-hidden="true"
     {{ $attributes->merge(['class' => $class]) }}>
    {!! $body !!}
</svg>
