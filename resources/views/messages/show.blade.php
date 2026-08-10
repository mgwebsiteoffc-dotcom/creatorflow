<x-layouts.app :panel="auth()->user()->creator ? 'creator' : 'brand'" :title="$thread->subject ?? 'Messages'">
    <a href="{{ route('messages.index') }}" class="text-sm text-slate-500">← Messages</a>
    <h1 class="mt-1 text-xl font-bold">{{ $thread->subject ?: ($thread->campaign->title ?? 'Conversation') }}</h1>

    <div class="card mt-4 flex h-[60vh] flex-col">
        <div class="flex-1 space-y-3 overflow-y-auto p-4">
            @foreach($messages as $m)
                @php
                    $me = (auth()->user()->creator && $m->sender_type === 'creator' && $m->sender_id === auth()->user()->creator->id)
                       || (! auth()->user()->creator && $m->sender_type === 'user' && $m->sender_id === auth()->id());
                @endphp
                <div class="flex {{ $me ? 'justify-end' : 'justify-start' }}">
                    <div class="max-w-[80%] rounded-2xl px-3.5 py-2 text-sm {{ $me ? 'bg-violet-600 text-white' : 'bg-slate-100 text-slate-800' }}">
                        {{ $m->body }}
                        <div class="mt-0.5 text-[10px] opacity-70">{{ $m->created_at->format('g:i A') }}</div>
                    </div>
                </div>
            @endforeach
        </div>
        <form method="POST" action="{{ route('messages.store', $thread) }}" class="border-t border-slate-100 p-3">
            @csrf
            <div class="flex gap-2">
                <input class="input" name="body" placeholder="Type a message…" autofocus required>
                <button class="btn-primary">Send</button>
            </div>
        </form>
    </div>
</x-layouts.app>
