{{-- Cmd-K / Ctrl-K command palette. Included on every authenticated layout. --}}
<div data-cmdk-root
     class="fixed inset-0 z-[60] hidden bg-slate-900/40 backdrop-blur-sm"
     aria-hidden="true">
    <div class="mx-auto mt-20 max-w-xl px-4">
        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-2xl">
            <div class="flex items-center gap-3 border-b border-slate-100 px-4 py-3">
                <x-icon name="search" class="h-4 w-4 text-slate-400" />
                <input data-cmdk-input type="search"
                       class="w-full bg-transparent text-sm outline-none placeholder:text-slate-400"
                       placeholder="Search creators, campaigns, products…"
                       autocomplete="off">
                <kbd class="hidden rounded-md border border-slate-200 bg-slate-50 px-1.5 py-0.5 text-[10px] font-semibold text-slate-500 md:inline">esc</kbd>
            </div>
            <div data-cmdk-list class="max-h-[60vh] overflow-y-auto p-2 text-sm">
                <div data-cmdk-empty class="px-3 py-8 text-center text-xs text-slate-500">
                    Type at least 2 characters to search.
                </div>
            </div>
            <div class="flex items-center justify-between border-t border-slate-100 bg-slate-50 px-4 py-2 text-[10px] text-slate-500">
                <span class="flex items-center gap-2">
                    <kbd class="rounded border border-slate-200 bg-white px-1 font-semibold">↑</kbd>
                    <kbd class="rounded border border-slate-200 bg-white px-1 font-semibold">↓</kbd>
                    Navigate
                    <kbd class="ml-3 rounded border border-slate-200 bg-white px-1 font-semibold">↵</kbd>
                    Open
                </span>
                <span>
                    <kbd class="rounded border border-slate-200 bg-white px-1 font-semibold">⌘</kbd>
                    +
                    <kbd class="rounded border border-slate-200 bg-white px-1 font-semibold">K</kbd>
                    to open anywhere
                </span>
            </div>
        </div>
    </div>
</div>

<script>
(function () {
    const root   = document.querySelector('[data-cmdk-root]');
    const input  = document.querySelector('[data-cmdk-input]');
    const list   = document.querySelector('[data-cmdk-list]');
    const empty  = document.querySelector('[data-cmdk-empty]');
    if (! root || ! input || ! list) return;

    const url   = @json(route('search.palette'));
    let selected = -1;
    let items    = [];
    let seq      = 0;
    let debounce;

    const iconSvg = (name) => {
        // Minimal fallback: the icon set is server-side; we just show a dot.
        const dotName = { campaigns:'●', creators:'●', products:'●', orders:'●', analytics:'●', billing:'●', plus:'+', admin:'★', marketplace:'●', applications:'●', work:'●', earnings:'●', profile:'●' };
        return `<span class="grid h-6 w-6 shrink-0 place-items-center rounded-md bg-slate-100 text-slate-500">${dotName[name] || '·'}</span>`;
    };

    const render = (data) => {
        const groups = {};
        (data.results || []).forEach(r => { (groups[r.group] = groups[r.group] || []).push(r); });

        let html = '';
        Object.keys(groups).forEach(g => {
            html += `<p class="mt-2 px-3 pb-1 text-[10px] font-bold uppercase tracking-widest text-slate-400">${g}</p>`;
            groups[g].forEach(r => {
                html += `<a href="${r.url}" data-cmdk-item class="flex items-center gap-3 rounded-lg px-3 py-2 text-slate-700 hover:bg-slate-100 hover:text-slate-900">
                    ${iconSvg(r.icon)}
                    <div class="min-w-0 flex-1">
                        <p class="truncate font-medium">${r.title}</p>
                        <p class="truncate text-[11px] text-slate-500">${r.meta || ''}</p>
                    </div>
                </a>`;
            });
        });

        if ((data.actions || []).length) {
            html += `<p class="mt-3 px-3 pb-1 text-[10px] font-bold uppercase tracking-widest text-slate-400">Quick actions</p>`;
            data.actions.forEach(a => {
                html += `<a href="${a.url}" data-cmdk-item class="flex items-center gap-3 rounded-lg px-3 py-2 text-slate-700 hover:bg-slate-100 hover:text-slate-900">
                    ${iconSvg(a.icon)}
                    <p class="min-w-0 flex-1 truncate font-medium">${a.title}</p>
                    ${a.shortcut ? `<span class="text-[10px] text-slate-400">${a.shortcut}</span>` : ''}
                </a>`;
            });
        }

        if (! html) {
            html = `<div class="px-3 py-8 text-center text-xs text-slate-500">No matches. Try a different search.</div>`;
        }

        list.innerHTML = html;
        items = list.querySelectorAll('[data-cmdk-item]');
        selected = items.length ? 0 : -1;
        updateSelected();
    };

    const updateSelected = () => {
        items.forEach((it, i) => it.classList.toggle('bg-slate-100', i === selected));
        if (selected >= 0) items[selected].scrollIntoView({ block: 'nearest' });
    };

    const doSearch = (q) => {
        const mine = ++seq;
        fetch(`${url}?q=${encodeURIComponent(q)}`, { headers: { 'Accept': 'application/json' } })
            .then(r => r.json())
            .then(data => { if (mine === seq) render(data); })
            .catch(() => {});
    };

    const open = () => {
        root.classList.remove('hidden');
        input.value = '';
        input.focus();
        // Show quick actions immediately.
        doSearch('');
    };
    const close = () => {
        root.classList.add('hidden');
        input.blur();
    };

    // Keyboard: Cmd/Ctrl+K opens, Esc closes, arrows navigate, Enter opens.
    document.addEventListener('keydown', (e) => {
        const isTypingInField = ['INPUT','TEXTAREA','SELECT'].includes(document.activeElement?.tagName) && document.activeElement !== input;
        if ((e.metaKey || e.ctrlKey) && e.key.toLowerCase() === 'k') {
            e.preventDefault();
            root.classList.contains('hidden') ? open() : close();
            return;
        }
        if (root.classList.contains('hidden') || isTypingInField && document.activeElement !== input) return;

        if (e.key === 'Escape') { close(); return; }
        if (! items.length) return;

        if (e.key === 'ArrowDown') { e.preventDefault(); selected = Math.min(items.length - 1, selected + 1); updateSelected(); }
        else if (e.key === 'ArrowUp') { e.preventDefault(); selected = Math.max(0, selected - 1); updateSelected(); }
        else if (e.key === 'Enter' && selected >= 0) {
            e.preventDefault();
            window.location.href = items[selected].getAttribute('href');
        }
    });

    // Click backdrop = close.
    root.addEventListener('click', (e) => { if (e.target === root) close(); });

    // Search on input, debounced.
    input.addEventListener('input', (e) => {
        clearTimeout(debounce);
        debounce = setTimeout(() => doSearch(e.target.value), 180);
    });

    // Expose a hook so buttons elsewhere can open the palette.
    window.CommandPalette = { open, close };
})();
</script>
