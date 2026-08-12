<x-layouts.admin title="Creators">
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <h1 class="text-3xl font-black tracking-tight text-slate-900">Creators</h1>
            <p class="mt-1 text-sm text-slate-500">Verify, ban, and bulk-import creators.</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.exports.creators') }}" class="btn-secondary">
                <x-icon name="download" class="h-4 w-4" /> Export CSV
            </a>
            <a href="{{ route('admin.creators.import') }}" class="btn-primary">
                <x-icon name="upload" class="h-4 w-4" /> Bulk import
            </a>
        </div>
    </div>

    <form method="GET" class="mt-6 grid gap-2 sm:grid-cols-[1fr_180px_160px_auto] sm:items-center">
        <input class="input" name="q" value="{{ request('q') }}" placeholder="Search creator…">
        <select class="input" name="status">
            <option value="">All statuses</option>
            @foreach(['active','pending','suspended'] as $s)
                <option value="{{ $s }}" @selected(request('status')===$s)>{{ ucfirst($s) }}</option>
            @endforeach
        </select>
        <select class="input" name="per_page" onchange="this.form.submit()">
            @foreach([25, 50, 100, 200] as $n)
                <option value="{{ $n }}" @selected((int) request('per_page', 25) === $n)>{{ $n }} / page</option>
            @endforeach
        </select>
        <button class="btn-secondary">Filter</button>
    </form>

    {{-- Bulk-action form wraps the table so every checkbox posts here --}}
    <form method="POST" action="{{ route('admin.creators.bulk') }}" data-bulk-form>
        @csrf

        {{-- Sticky action bar — shows only when at least one row is checked --}}
        <div data-bulk-bar
             class="sticky top-16 z-20 mt-6 hidden overflow-hidden rounded-2xl border border-violet-200 bg-gradient-to-r from-violet-50 to-pink-50 shadow-sm">
            <div class="flex flex-wrap items-center justify-between gap-3 px-4 py-3">
                <p class="text-sm font-semibold text-slate-800">
                    <span data-bulk-count>0</span> creator(s) selected
                </p>
                <div class="flex flex-wrap items-center gap-1.5">
                    <button type="submit" name="action" value="approve"       class="btn-secondary !py-1.5 !text-xs">Approve</button>
                    <button type="submit" name="action" value="tag_verified"  class="btn-secondary !py-1.5 !text-xs">Mark verified</button>
                    <button type="submit" name="action" value="tag_pending"   class="btn-secondary !py-1.5 !text-xs">Send to pending</button>
                    <button type="submit" name="action" value="suspend"       class="btn-secondary !py-1.5 !text-xs !text-rose-600 hover:!bg-rose-50" data-bulk-confirm="Suspend the selected creators?">Suspend</button>
                    <button type="submit" name="action" value="delete"        class="btn-danger !py-1.5 !text-xs" data-bulk-confirm="Delete selected? Only creators with zero assignments will be removed.">Delete</button>
                    <button type="button" data-bulk-clear class="btn-ghost !py-1.5 !text-xs">Clear</button>
                </div>
            </div>
        </div>

        <div class="mt-4 overflow-x-auto rounded-2xl border border-slate-200 bg-white">
            <table class="w-full min-w-[720px] text-sm">
                <thead class="border-b border-slate-200 bg-slate-50 text-left text-xs uppercase tracking-widest text-slate-500">
                    <tr>
                        <th class="w-10 p-3">
                            <input type="checkbox" data-bulk-select-all class="h-4 w-4 rounded border-slate-300 text-violet-600 focus:ring-violet-400" aria-label="Select all">
                        </th>
                        <th class="p-3">Creator</th>
                        <th class="p-3">Followers</th>
                        <th class="p-3">Status</th>
                        <th class="p-3">Assignments</th>
                        <th class="p-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($creators as $c)
                        <tr data-bulk-row>
                            <td class="p-3">
                                <input type="checkbox" name="creator_ids[]" value="{{ $c->id }}"
                                       data-bulk-checkbox
                                       class="h-4 w-4 rounded border-slate-300 text-violet-600 focus:ring-violet-400"
                                       aria-label="Select {{ $c->display_name }}">
                            </td>
                            <td class="p-3">
                                <a href="{{ route('admin.creators.show', $c) }}" class="flex items-center gap-3 group">
                                    <div class="grid h-9 w-9 shrink-0 place-items-center rounded-full bg-gradient-to-br from-rose-500 to-pink-500 font-bold text-white">{{ strtoupper(substr($c->display_name,0,1)) }}</div>
                                    <div class="min-w-0">
                                        <div class="truncate font-semibold text-slate-900 group-hover:text-violet-700">{{ $c->display_name }}</div>
                                        <div class="truncate text-xs text-slate-500">{{ $c->email ?: '—' }}</div>
                                    </div>
                                </a>
                            </td>
                            <td class="p-3 text-slate-600 tabular-nums">{{ number_format($c->follower_count_total) }}</td>
                            <td class="p-3">
                                <x-badge :tone="$c->status === 'active' ? 'green' : ($c->status === 'suspended' ? 'rose' : 'amber')">{{ $c->status }}</x-badge>
                            </td>
                            <td class="p-3 text-slate-600 tabular-nums">{{ $c->assignments_count }}</td>
                            <td class="p-3 text-right">
                                @if($c->status === 'suspended')
                                    <button type="button"
                                            data-single-action="reinstate"
                                            data-single-url="{{ route('admin.creators.reinstate', $c) }}"
                                            class="btn-primary !py-1 !text-xs">Reinstate</button>
                                @else
                                    <button type="button"
                                            data-single-action="ban"
                                            data-single-url="{{ route('admin.creators.suspend', $c) }}"
                                            data-single-name="{{ $c->display_name }}"
                                            class="btn-ghost !py-1 !text-xs !text-rose-600 hover:!bg-rose-50">Ban</button>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </form>

    {{-- Hidden single-row action form (kept out of the bulk form so it doesn't pick up creator_ids[]) --}}
    <form method="POST" id="single-action-form" class="hidden">
        @csrf
        <input type="hidden" name="reason" value="Suspended by admin">
    </form>

    <div class="mt-6">{{ $creators->links() }}</div>

    <script>
    (function () {
        const form    = document.querySelector('[data-bulk-form]');
        const bar     = document.querySelector('[data-bulk-bar]');
        const count   = document.querySelector('[data-bulk-count]');
        const master  = document.querySelector('[data-bulk-select-all]');
        const boxes   = form ? form.querySelectorAll('[data-bulk-checkbox]') : [];
        const clearBt = document.querySelector('[data-bulk-clear]');
        const single  = document.getElementById('single-action-form');
        const csrf    = document.querySelector('meta[name="csrf-token"]')?.content || document.querySelector('input[name="_token"]')?.value || '';

        function refresh() {
            const selected = Array.from(boxes).filter(b => b.checked).length;
            if (! bar) return;
            bar.classList.toggle('hidden', selected === 0);
            if (count) count.textContent = selected;
            if (master) master.checked = selected > 0 && selected === boxes.length;
        }

        master?.addEventListener('change', () => {
            boxes.forEach(b => b.checked = master.checked);
            refresh();
        });
        boxes.forEach(b => b.addEventListener('change', refresh));
        clearBt?.addEventListener('click', () => { boxes.forEach(b => b.checked = false); refresh(); });

        // Confirm dialogs on destructive bulk buttons
        form?.querySelectorAll('[data-bulk-confirm]').forEach(btn => {
            btn.addEventListener('click', (e) => {
                if (! confirm(btn.dataset.bulkConfirm)) e.preventDefault();
            });
        });

        // Single-row buttons submit a separate form so they don't accidentally
        // pull in the currently-checked row set from the bulk form.
        document.querySelectorAll('[data-single-action]').forEach(btn => {
            btn.addEventListener('click', () => {
                if (btn.dataset.singleAction === 'ban' &&
                    ! confirm('Ban ' + (btn.dataset.singleName || 'this creator') + '?')) return;
                single.action = btn.dataset.singleUrl;
                single.submit();
            });
        });
    })();
    </script>
</x-layouts.admin>
