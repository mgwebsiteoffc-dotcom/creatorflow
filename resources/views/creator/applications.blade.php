<x-layouts.app panel="creator" title="My applications">
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <h1 class="text-3xl font-black tracking-tight text-slate-900">My applications</h1>
            <p class="mt-1 text-sm text-slate-500">Every campaign you've applied to and where it stands.</p>
        </div>
        <a href="{{ route('creator.marketplace') }}" class="btn-primary !py-2 text-sm">Browse marketplace →</a>
    </div>

    {{-- Status tabs --}}
    @php
        $tabs = [
            'all' => ['label' => 'All', 'tone' => 'slate'],
            'submitted' => ['label' => 'Pending', 'tone' => 'amber'],
            'shortlisted' => ['label' => 'Shortlisted', 'tone' => 'sky'],
            'approved' => ['label' => 'Approved', 'tone' => 'green'],
            'rejected' => ['label' => 'Rejected', 'tone' => 'rose'],
        ];
        $active = request('status', 'all');
    @endphp
    <div class="mt-6 flex flex-wrap gap-2">
        @foreach($tabs as $key => $t)
            <a href="{{ $key === 'all' ? route('creator.applications') : route('creator.applications', ['status' => $key]) }}"
               class="tab-pill {{ $active === $key ? 'is-active' : '' }}">
                {{ $t['label'] }}
                <span class="ml-1 rounded-full bg-black/10 px-1.5 py-0.5 text-[10px] font-bold {{ $active === $key ? 'text-white' : 'text-slate-500' }}">{{ $counts[$key] ?? 0 }}</span>
            </a>
        @endforeach
    </div>

    <div class="mt-6 space-y-3">
        @forelse($applications as $app)
            @php
                $badgeTone = match($app->status) {
                    'submitted' => 'amber',
                    'shortlisted' => 'sky',
                    'approved' => 'green',
                    'rejected' => 'rose',
                    'withdrawn' => 'slate',
                    default => 'slate',
                };
                $badgeLabel = match($app->status) {
                    'submitted' => 'Pending review',
                    'shortlisted' => 'Shortlisted',
                    'approved' => 'Approved ',
                    'rejected' => 'Not selected',
                    'withdrawn' => 'Withdrawn',
                    default =>ucfirst($app->status),
                };
            @endphp
            <div class="card p-5">
                <div class="flex items-start gap-4">
                    <div class="grid h-12 w-12 shrink-0 place-items-center rounded-2xl bg-gradient-to-br from-rose-500 to-pink-500 text-lg text-white"></div>
                    <div class="min-w-0 flex-1">
                        <div class="flex flex-wrap items-center gap-2">
                            <a href="{{ route('creator.marketplace.show', $app->campaign) }}" class="font-bold text-slate-900 hover:text-violet-700">{{ $app->campaign->title }}</a>
                            <x-badge :tone="$badgeTone">{{ $badgeLabel }}</x-badge>
                            <span class="badge-slate capitalize">{{ $app->campaign->type }}</span>
                        </div>
                        <p class="mt-0.5 text-xs text-slate-500">
                            {{ $app->campaign->workspace->name ?? 'Brand' }} · applied {{ $app->created_at->diffForHumans() }}
                        </p>
                        @if($app->cover_note)
                            <p class="mt-3 line-clamp-2 rounded-xl bg-slate-50 p-3 text-sm text-slate-600">
                                <span class="text-xs font-bold uppercase tracking-widest text-slate-400">Your note</span><br>
                                {{ $app->cover_note }}
                            </p>
                        @endif
                        @if($app->status === 'approved')
                            <p class="mt-3 text-sm text-emerald-700">
                                 The brand approved you — check your <a href="{{ route('creator.invitations') }}" class="font-semibold underline">Invitations</a>to accept and start.
                            </p>
                        @endif
                    </div>
                    <div class="flex shrink-0 flex-col items-end gap-2">
                        <a href="{{ route('creator.marketplace.show', $app->campaign) }}" class="btn-ghost !py-1.5 text-xs">View campaign</a>
                        @if(in_array($app->status, ['submitted','shortlisted']))
                            <form method="POST" action="{{ route('creator.applications.withdraw', $app) }}"
                                  data-confirm="Withdraw this application?">
                                @csrf
                                <button class="btn-secondary !py-1.5 text-xs">Withdraw</button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <x-empty-state title="No applications yet" icon="applications">
                Browse open campaigns and apply — brands respond in ~48h.
                <x-slot:action><a href="{{ route('creator.marketplace') }}" class="btn-primary">Browse marketplace</a></x-slot:action>
            </x-empty-state>
        @endforelse
    </div>

    <div class="mt-6">{{ $applications->links() }}</div>
</x-layouts.app>
