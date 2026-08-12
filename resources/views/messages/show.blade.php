<x-layouts.app :panel="auth()->user()->creator ? 'creator' : 'brand'" :title="$thread->subject ?? 'Messages'">
    <a href="{{ route('messages.index') }}" class="text-sm text-slate-500">← Messages</a>
    <div class="mt-1 flex flex-wrap items-center justify-between gap-3">
        <div>
            <h1 class="text-xl font-black text-slate-900">{{ $thread->subject ?: ($thread->campaign->title ?? 'Conversation') }}</h1>
            @if($thread->campaign)
                <p class="text-xs text-slate-500">Campaign: {{ $thread->campaign->title }}</p>
            @endif
        </div>
        <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-3 py-1 text-[11px] font-semibold text-emerald-700" data-live-pill>
            <span class="relative flex h-2 w-2"><span class="absolute inset-0 animate-ping rounded-full bg-emerald-400 opacity-75"></span><span class="relative inline-block h-2 w-2 rounded-full bg-emerald-500"></span></span>
            Live
        </span>
    </div>

    <div class="card mt-4 flex h-[65vh] flex-col overflow-hidden">
        <div data-thread class="flex-1 space-y-3 overflow-y-auto p-4" data-latest="{{ $messages->last()->id ?? 0 }}">
            @foreach($messages as $m)
                @php
                    $me = (auth()->user()->creator && $m->sender_type === 'creator' && $m->sender_id === auth()->user()->creator->id)
                       || (! auth()->user()->creator && $m->sender_type === 'user' && $m->sender_id === auth()->id());
                @endphp
                <div class="flex {{ $me ? 'justify-end' : 'justify-start' }}" data-msg-id="{{ $m->id }}">
                    <div class="max-w-[80%] rounded-2xl px-3.5 py-2 text-sm shadow-sm {{ $me ? 'bg-gradient-to-br from-violet-600 to-pink-500 text-white' : 'bg-slate-100 text-slate-800' }}">
                        <div class="whitespace-pre-wrap break-words">{{ $m->body }}</div>
                        <div class="mt-0.5 text-[10px] {{ $me ? 'text-white/70' : 'text-slate-500' }}">{{ $m->created_at->format('g:i A') }}</div>
                    </div>
                </div>
            @endforeach
            @if($messages->isEmpty())
                <div class="grid h-full place-items-center text-center text-sm text-slate-400">
                    <div><p class="text-3xl">💬</p><p class="mt-2">Say hi to kick off the conversation.</p></div>
                </div>
            @endif
        </div>
        <form data-msg-form method="POST" action="{{ route('messages.store', $thread) }}" class="border-t border-slate-100 p-3">
            @csrf
            <div class="flex gap-2">
                <input class="input" name="body" placeholder="Type a message…" autocomplete="off" autofocus required maxlength="4000">
                <button class="btn-primary">Send</button>
            </div>
            <p class="mt-1 text-[10px] text-slate-400"><span data-online-status>Auto-refreshes every 15 s</span></p>
        </form>
    </div>

    <script>
    (function() {
        const thread    = document.querySelector('[data-thread]');
        const form      = document.querySelector('[data-msg-form]');
        const input     = form.querySelector('input[name="body"]');
        const status    = document.querySelector('[data-online-status]');
        const livePill  = document.querySelector('[data-live-pill]');
        const meId      = @json(auth()->user()->creator?->id ?? auth()->id());
        const meType    = @json(auth()->user()->creator ? 'creator' : 'user');
        let latestId    = Number(thread.dataset.latest || 0);
        let polling     = false;

        const scrollBottom = () => { thread.scrollTop = thread.scrollHeight; };
        scrollBottom();

        // Render a message bubble and append it.
        const bubble = (m) => {
            const wrap = document.createElement('div');
            wrap.className = 'flex ' + (m.mine ? 'justify-end' : 'justify-start');
            wrap.dataset.msgId = m.id;
            const meClasses  = 'bg-gradient-to-br from-violet-600 to-pink-500 text-white';
            const themClasses= 'bg-slate-100 text-slate-800';
            wrap.innerHTML = `
                <div class="max-w-[80%] rounded-2xl px-3.5 py-2 text-sm shadow-sm ${m.mine ? meClasses : themClasses}">
                    <div class="whitespace-pre-wrap break-words"></div>
                    <div class="mt-0.5 text-[10px] ${m.mine ? 'text-white/70' : 'text-slate-500'}">${m.time}</div>
                </div>`;
            wrap.querySelector('div > div').textContent = m.body;
            return wrap;
        };

        // Poll for new messages.
        const pollUrl = @json(route('messages.poll', $thread));
        async function poll() {
            if (polling) return;
            polling = true;
            try {
                const res = await fetch(pollUrl + '?since=' + latestId, {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                    credentials: 'same-origin',
                });
                if (!res.ok) throw new Error('Poll failed ' + res.status);
                const data = await res.json();
                if (data.messages && data.messages.length) {
                    // Only append messages we don't already have.
                    for (const m of data.messages) {
                        if (thread.querySelector('[data-msg-id="' + m.id + '"]')) continue;
                        thread.appendChild(bubble(m));
                        latestId = Math.max(latestId, m.id);
                    }
                    thread.dataset.latest = latestId;
                    scrollBottom();
                    if (livePill) {
                        livePill.animate([{transform:'scale(1)'},{transform:'scale(1.05)'},{transform:'scale(1)'}], {duration:400});
                    }
                }
                status.textContent = 'Live · updated ' + new Date().toLocaleTimeString([], {hour: '2-digit', minute: '2-digit', second:'2-digit'});
                if (livePill) { livePill.classList.remove('bg-rose-50','text-rose-700'); livePill.classList.add('bg-emerald-50','text-emerald-700'); }
            } catch (e) {
                status.textContent = 'Offline — retrying…';
                if (livePill) { livePill.classList.add('bg-rose-50','text-rose-700'); livePill.classList.remove('bg-emerald-50','text-emerald-700'); }
            } finally {
                polling = false;
            }
        }

        // Optimistic-send: append bubble instantly, POST, replace with server copy on poll.
        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            const body = input.value.trim();
            if (!body) return;
            const tempId = 'tmp-' + Date.now();
            const optimistic = bubble({ id: tempId, mine: true, body, time: new Date().toLocaleTimeString([], {hour:'2-digit', minute:'2-digit'}) });
            optimistic.style.opacity = '.55';
            thread.appendChild(optimistic);
            scrollBottom();
            input.value = '';
            input.focus();

            try {
                const fd = new FormData(form);
                fd.set('body', body);
                const res = await fetch(form.action, {
                    method: 'POST',
                    body: fd,
                    headers: { 'Accept':'text/html', 'X-Requested-With':'XMLHttpRequest' },
                    credentials: 'same-origin',
                });
                if (!res.ok && res.status !== 302) throw new Error('Send failed');
                await poll(); // pull the real message with the real id + timestamp
                optimistic.remove();
            } catch (e) {
                optimistic.style.opacity = '1';
                optimistic.querySelector('div > div').textContent = body + ' ⚠︎ failed — tap to retry';
                optimistic.style.cursor = 'pointer';
                optimistic.addEventListener('click', () => { optimistic.remove(); input.value = body; form.requestSubmit(); }, { once: true });
            }
        });

        // Poll every 15s while the tab is visible. Instant catch-up when tab wakes.
        let interval = setInterval(poll, 15_000);
        document.addEventListener('visibilitychange', () => {
            if (document.hidden) {
                clearInterval(interval);
            } else {
                poll();
                interval = setInterval(poll, 15_000);
            }
        });
    })();
    </script>
</x-layouts.app>
