<x-layouts.app :panel="auth()->user()->creator ? 'creator' : 'brand'" title="Notifications">
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <h1 class="text-3xl font-black tracking-tight text-slate-900">Notifications</h1>
            <p class="mt-1 text-sm text-slate-500">Everything that happened across your campaigns.</p>
        </div>
        @if($notifications->total() > 0)
            <form method="POST" action="{{ route('notifications.readAll') }}">
                @csrf
                <button class="btn-secondary !py-2 text-sm">Mark all read</button>
            </form>
        @endif
    </div>

    <div class="mt-8 space-y-2">
        @forelse($notifications as $n)
            <a href="{{ route('notifications.open', $n) }}"
               class="flex items-start gap-3 rounded-2xl border p-4 transition
                      {{ $n->read_at ? 'border-slate-200 bg-white hover:border-slate-300' : 'border-violet-200 bg-violet-50/40 hover:border-violet-300' }}">
                <div class="grid h-10 w-10 shrink-0 place-items-center rounded-xl text-lg
                            {{ str_starts_with($n->type, 'content.approved') ? 'bg-emerald-100 text-emerald-700' :
                               (str_starts_with($n->type, 'content.') ? 'bg-amber-100 text-amber-700' :
                               'bg-violet-100 text-violet-700') }}">
                    {{ str_contains($n->type, 'approved') ? '✅' : (str_contains($n->type, 'content') ? '🎬' : '🔔') }}
                </div>
                <div class="min-w-0 flex-1">
                    <div class="flex items-center gap-2">
                        <p class="font-semibold text-slate-900">{{ $n->data['title'] ?? ucfirst(str_replace('.', ' ', $n->type)) }}</p>
                        @if(! $n->read_at)<span class="h-2 w-2 rounded-full bg-violet-600"></span>@endif
                    </div>
                    @if($n->data['body'] ?? null)
                        <p class="mt-0.5 text-sm text-slate-600">{{ $n->data['body'] }}</p>
                    @endif
                    <p class="mt-1 text-xs text-slate-400">{{ $n->created_at->diffForHumans() }}</p>
                </div>
                <svg class="h-4 w-4 shrink-0 text-slate-300" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" d="M9 5l7 7-7 7"/></svg>
            </a>
        @empty
            <x-empty-state title="You're all caught up" icon="🎉">
                Nothing to review right now. New submissions, approvals, applications and payouts will show up here.
            </x-empty-state>
        @endforelse
    </div>

    <div class="mt-8">{{ $notifications->links() }}</div>
</x-layouts.app>
