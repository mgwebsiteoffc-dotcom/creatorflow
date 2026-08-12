@php
    /** @var string $panel */
    $panel = $panel ?? 'brand';
    $workspace = $workspace ?? ($currentWorkspace ?? null);
    $logoGrad = $panel === 'creator'
        ? 'linear-gradient(135deg,#f43f5e,#ec4899 60%,#f59e0b)'
        : 'linear-gradient(135deg,#7c3aed,#ec4899 60%,#f59e0b)';
@endphp

{{-- Slim topbar. Sidebar owns primary nav; this only carries:
     · mobile hamburger (opens sidebar as a drawer on <md)
     · title / current section (optional, breadcrumb-like)
     · notifications bell · messages · workspace pill · sign-out
--}}
<header class="sticky top-0 z-30 border-b border-slate-200 bg-white/85 backdrop-blur">
    <div class="mx-auto flex h-14 w-full items-center justify-between gap-2 px-4 md:h-16 md:px-6 lg:px-8">
        {{-- Left: hamburger (mobile) + compact brand mark (mobile) --}}
        <div class="flex min-w-0 items-center gap-2">
            <button type="button" data-sidebar-open
                    class="grid h-9 w-9 place-items-center rounded-lg text-slate-600 hover:bg-slate-100 md:hidden"
                    aria-label="Open menu">
                <x-icon name="menu" class="h-5 w-5" />
            </button>
            <a href="{{ url('/') }}" class="flex items-center gap-2 md:hidden">
                <span class="grid h-8 w-8 place-items-center rounded-xl text-xs font-black text-white shadow-sm"
                      style="background-image: {{ $logoGrad }};">CP</span>
                <span class="text-sm font-bold text-slate-900">CreatorPlex</span>
            </a>
        </div>

        {{-- Right: bell · messages · workspace name · sign-out --}}
        <div class="flex items-center gap-1.5">
            {{-- Notifications bell --}}
            <div class="relative" data-notif-wrap>
                <button type="button" data-notif-toggle class="btn-ghost relative !p-2" title="Notifications">
                    <x-icon name="bell" class="h-5 w-5" />
                    @if(($unreadCount ?? 0) >0)
                        <span class="absolute -right-0.5 -top-0.5 grid h-4 min-w-4 place-items-center rounded-full bg-rose-500 px-1 text-[10px] font-bold text-white">{{ min(9, $unreadCount) }}{{ $unreadCount >9 ? '+' : '' }}</span>
                    @endif
                </button>
                <div data-notif-panel class="absolute right-0 top-full z-50 mt-2 hidden w-80 overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-xl">
                    <div class="flex items-center justify-between border-b border-slate-100 px-4 py-3">
                        <div>
                            <p class="text-sm font-bold text-slate-900">Notifications</p>
                            <p class="text-[11px] text-slate-500">{{ $unreadCount ?? 0 }} unread</p>
                        </div>
                        <a href="{{ route('notifications.index') }}" class="text-xs font-semibold text-violet-700 hover:text-violet-900">See all →</a>
                    </div>
                    <div class="max-h-96 overflow-y-auto">
                        @forelse(($recentNotifications ?? []) as $n)
                            <a href="{{ route('notifications.open', $n) }}"
                               class="flex items-start gap-2 border-b border-slate-100 px-4 py-3 last:border-0 {{ $n->read_at ? '' : 'bg-violet-50/40' }} hover:bg-slate-50">
                                @php
                                    $nIcon = str_contains($n->type, 'approved') ? 'check-circle'
                                          : (str_contains($n->type, 'content')  ? 'video'
                                          : 'bell');
                                @endphp
                                <x-icon :name="$nIcon" class="mt-0.5 h-4 w-4 text-slate-500" />
                                <div class="min-w-0 flex-1">
                                    <p class="truncate text-sm font-semibold text-slate-900">{{ $n->data['title'] ?? '' }}</p>
                                    @if($n->data['body'] ?? null)<p class="line-clamp-2 text-xs text-slate-500">{{ $n->data['body'] }}</p>@endif
                                    <p class="mt-0.5 text-[11px] text-slate-400">{{ $n->created_at->diffForHumans() }}</p>
                                </div>
                                @if(! $n->read_at)<span class="mt-2 h-2 w-2 shrink-0 rounded-full bg-violet-600"></span>@endif
                            </a>
                        @empty
                            <div class="px-4 py-8 text-center text-xs text-slate-500">You're all caught up </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <a href="{{ route('messages.index') }}" class="btn-ghost !p-2" title="Messages">
                <x-icon name="messages" class="h-5 w-5" />
            </a>

            @if($panel === 'brand' && $workspace)
                <span class="hidden max-w-[160px] truncate rounded-lg bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-600 sm:inline-block">
                    {{ $workspace->name }}
                </span>
            @endif
        </div>
    </div>
</header>

<script>
    (function () {
        const sidebar = document.querySelector('[data-sidebar]');
        if (! sidebar) return;
        const openBtn = document.querySelector('[data-sidebar-open]');
        const closeBtn = document.querySelector('[data-sidebar-close]');
        const backdrop = document.querySelector('[data-sidebar-backdrop]');

        const open = () => {
            sidebar.classList.remove('hidden');
            sidebar.classList.add('flex');
            backdrop?.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        };
        const close = () => {
            if (window.innerWidth >= 768) return; // md+ keeps sidebar always visible
            sidebar.classList.add('hidden');
            sidebar.classList.remove('flex');
            backdrop?.classList.add('hidden');
            document.body.style.overflow = '';
        };

        openBtn?.addEventListener('click', open);
        closeBtn?.addEventListener('click', close);
        backdrop?.addEventListener('click', close);

        // Close drawer when a nav link is tapped on mobile
        sidebar.querySelectorAll('a[href]').forEach(a =>a.addEventListener('click', () => {
            if (window.innerWidth < 768) close();
        }));
    })();
</script>
