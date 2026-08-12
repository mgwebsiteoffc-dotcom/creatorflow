<x-layouts.app :panel="$isCreator ? 'creator' : 'brand'" title="Messages">
    <h1 class="text-2xl font-bold">Messages</h1>

    <div class="mt-5 space-y-2">
        @forelse($threads as $t)
            <a href="{{ route('messages.show', $t) }}" class="card flex items-center gap-3 p-3">
                <div class="grid h-10 w-10 place-items-center rounded-full bg-violet-100"></div>
                <div class="min-w-0 flex-1">
                    <p class="truncate text-sm font-semibold">{{ $t->subject ?: ($t->campaign->title ?? 'Conversation') }}</p>
                    <p class="truncate text-xs text-slate-500">{{ $t->campaign?->title ?: 'Direct message' }} · {{ $t->messages_count }} messages</p>
                </div>
                <span class="text-xs text-slate-400">{{ $t->updated_at?->diffForHumans() }}</span>
            </a>
        @empty
            <x-empty-state title="No messages yet" icon="messages">
                Start a conversation from a campaign or creator profile.
            </x-empty-state>
        @endforelse
    </div>
</x-layouts.app>
