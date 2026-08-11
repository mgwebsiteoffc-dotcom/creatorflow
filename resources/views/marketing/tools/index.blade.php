<x-layouts.app panel="guest" title="Free tools">
    @include('marketing._hero', [
        'eyebrow' => 'Free tools · No signup',
        'title'   => 'Free <span class="text-gradient">creator marketing</span> tools',
        'sub'     => 'Calculators, generators, and templates built by the CreatorFlow team. All free, no signup required.',
        'ctaText' => 'Try a tool',
        'ctaHref' => '#grid',
    ])

    <section id="grid" class="mx-auto max-w-6xl px-4 pb-24">
        <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
            @php
                $tools = [
                    [
                        'route' => 'tools.roi',
                        'icon'  => '📊',
                        'title' => 'ROI Calculator',
                        'desc'  => 'Model reach, ER, and revenue for your next creator campaign in seconds.',
                        'grad'  => 'from-violet-500 to-pink-500',
                        'chip'  => 'Most used',
                    ],
                    [
                        'route' => 'tools.rate',
                        'icon'  => '💸',
                        'title' => 'Creator rate calculator',
                        'desc'  => 'What should a creator charge (or a brand pay) for UGC, Reels, and videos.',
                        'grad'  => 'from-cyan-500 to-emerald-500',
                        'chip'  => 'New',
                    ],
                    [
                        'route' => 'tools.brief',
                        'icon'  => '📝',
                        'title' => 'AI brief generator',
                        'desc'  => 'Paste your product, get a launch-ready creator brief in one click.',
                        'grad'  => 'from-amber-500 to-rose-500',
                        'chip'  => 'AI',
                    ],
                    [
                        'route' => 'tools.roi',
                        'icon'  => '🎯',
                        'title' => 'Audience overlap',
                        'desc'  => 'Coming soon — spot which creators share audiences before you spend.',
                        'grad'  => 'from-indigo-500 to-violet-500',
                        'chip'  => 'Soon',
                    ],
                    [
                        'route' => 'tools.roi',
                        'icon'  => '📅',
                        'title' => 'Content calendar',
                        'desc'  => 'Coming soon — plan drops and creator collabs across 12 weeks.',
                        'grad'  => 'from-emerald-500 to-teal-500',
                        'chip'  => 'Soon',
                    ],
                    [
                        'route' => 'tools.brief',
                        'icon'  => '🎬',
                        'title' => 'Hook generator',
                        'desc'  => 'Coming soon — 20 viral-worthy hooks for your product in 5 seconds.',
                        'grad'  => 'from-fuchsia-500 to-purple-600',
                        'chip'  => 'Soon',
                    ],
                ];
            @endphp
            @foreach($tools as $t)
                <a href="{{ route($t['route']) }}" class="reveal card card-hover group relative flex flex-col overflow-hidden p-6">
                    <div class="absolute -right-8 -top-8 h-28 w-28 rounded-full bg-gradient-to-br {{ $t['grad'] }} opacity-25 blur-2xl transition-transform duration-500 group-hover:scale-125"></div>
                    <div class="relative">
                        <div class="flex items-center justify-between">
                            <span class="grid h-11 w-11 place-items-center rounded-xl bg-gradient-to-br {{ $t['grad'] }} text-lg text-white shadow-sm">{{ $t['icon'] }}</span>
                            <span class="badge badge-violet">{{ $t['chip'] }}</span>
                        </div>
                        <h3 class="mt-4 text-lg font-bold text-slate-900">{{ $t['title'] }}</h3>
                        <p class="mt-2 text-sm text-slate-600">{{ $t['desc'] }}</p>
                        <span class="mt-4 inline-flex items-center gap-1 text-sm font-semibold text-violet-700 group-hover:text-violet-900">
                            Open tool
                            <svg class="h-4 w-4 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" d="M5 12h14M13 6l6 6-6 6"/></svg>
                        </span>
                    </div>
                </a>
            @endforeach
        </div>
    </section>

    @include('marketing._cta', ['title' => 'Want the tools inside your workflow?', 'sub' => 'CreatorFlow bakes these into a full campaign engine. Free to start.'])
</x-layouts.app>
