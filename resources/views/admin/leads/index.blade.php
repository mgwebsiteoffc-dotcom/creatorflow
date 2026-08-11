<x-layouts.admin title="Leads">
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <h1 class="text-3xl font-black tracking-tight text-slate-900">Leads</h1>
            <p class="mt-1 text-sm text-slate-500">Contact-form submissions from the marketing site.</p>
        </div>
    </div>

    @if(! empty($schemaMissing))
        <div class="mt-6 flex items-start gap-3 rounded-2xl border border-amber-300 bg-amber-50 p-4 text-sm text-amber-900">
            <span class="grid h-9 w-9 shrink-0 place-items-center rounded-xl bg-gradient-to-br from-amber-500 to-orange-500 text-white shadow-sm">⚠</span>
            <div class="flex-1"><p class="font-bold">leads table not migrated yet.</p><p class="mt-1 text-xs text-amber-800">Run <code class="rounded bg-white/70 px-1.5 py-0.5">php artisan migrate</code>. Contact-form submissions will populate here after migration.</p></div>
        </div>
    @endif

    <div class="mt-6 flex flex-wrap gap-2">
        @foreach(['all','new','contacted','qualified','won','lost'] as $s)
            <a href="{{ route('admin.leads.index', ['status' => $s]) }}" class="tab-pill {{ $status === $s ? 'is-active' : '' }}">
                {{ ucfirst($s) }}
                <span class="ml-1 rounded-full bg-black/10 px-1.5 py-0.5 text-[10px] font-bold {{ $status === $s ? 'text-white' : 'text-slate-500' }}">{{ $counts[$s] ?? 0 }}</span>
            </a>
        @endforeach
    </div>

    <div class="mt-6 space-y-3">
        @forelse($leads as $l)
            <details class="rounded-2xl border border-slate-200 bg-white p-4">
                <summary class="flex cursor-pointer flex-wrap items-center gap-3">
                    <div class="grid h-10 w-10 place-items-center rounded-full bg-slate-100 font-bold text-slate-500">{{ strtoupper(substr($l->name,0,1)) }}</div>
                    <div class="min-w-0 flex-1">
                        <p class="truncate text-sm font-semibold text-slate-900">{{ $l->name }} <span class="text-xs font-normal text-slate-500">· {{ $l->email }}</span></p>
                        <p class="truncate text-xs text-slate-500">{{ $l->company ?: '—' }} · {{ ucfirst($l->reason ?? 'other') }} · {{ $l->created_at->diffForHumans() }}</p>
                    </div>
                    <x-badge :tone="match($l->status) { 'new' => 'amber', 'qualified' => 'sky', 'won' => 'green', 'lost' => 'rose', default => 'slate' }">{{ $l->status }}</x-badge>
                </summary>
                <div class="mt-4 space-y-3 border-t border-slate-100 pt-4">
                    @if($l->message)<p class="rounded-xl bg-slate-50 p-3 text-sm text-slate-700">{{ $l->message }}</p>@endif
                    <form method="POST" action="{{ route('admin.leads.update', $l) }}" class="flex flex-wrap items-end gap-2">
                        @csrf
                        <div>
                            <label class="label">Status</label>
                            <select name="status" class="input">
                                @foreach(['new','contacted','qualified','won','lost'] as $s)
                                    <option value="{{ $s }}" @selected($l->status === $s)>{{ ucfirst($s) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="min-w-[240px] flex-1">
                            <label class="label">Note</label>
                            <input name="note" class="input" value="{{ $l->note }}" placeholder="Internal note…">
                        </div>
                        <button class="btn-primary !py-2 text-sm">Save</button>
                    </form>
                </div>
            </details>
        @empty
            <x-empty-state title="No leads in this bucket" icon="📥">Leads land here when someone submits the marketing contact form.</x-empty-state>
        @endforelse
    </div>

    <div class="mt-6">{{ $leads->links() }}</div>
</x-layouts.admin>
