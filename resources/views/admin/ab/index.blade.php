<x-layouts.admin title="A/B experiments">
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <p class="text-xs font-bold uppercase tracking-widest text-violet-600">Superadmin · Growth</p>
            <h1 class="mt-1 text-3xl font-black tracking-tight text-slate-900">🧪 A/B experiments</h1>
            <p class="mt-1 text-sm text-slate-500">Test hero copy, CTAs, pricing framing. Use <code class="rounded bg-slate-100 px-1.5">\App\Support\AbTest::variant('slug')</code> in any Blade view.</p>
        </div>
        <a href="{{ route('admin.ab.create') }}" class="btn-gradient !py-2 text-sm">+ New experiment</a>
    </div>

    <div class="mt-8 overflow-x-auto rounded-2xl border border-slate-200 bg-white">
        <table class="w-full min-w-[640px] text-sm">
            <thead class="border-b border-slate-200 bg-slate-50 text-left text-xs uppercase tracking-widest text-slate-500">
                <tr><th class="p-3">Experiment</th><th class="p-3">Surface</th><th class="p-3">Variants</th><th class="p-3">Impressions</th><th class="p-3">Conversions</th><th class="p-3">Status</th><th class="p-3 text-right">Actions</th></tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($experiments as $e)
                    @php
                        $eStats = $stats[$e->id] ?? collect();
                        $imp = (int) $eStats->where('event', 'impression')->sum('cnt');
                        $conv = (int) $eStats->where('event', 'conversion')->sum('cnt');
                    @endphp
                    <tr>
                        <td class="p-3">
                            <a href="{{ route('admin.ab.edit', $e) }}" class="font-black text-slate-900 hover:text-violet-700">{{ $e->name }}</a>
                            <div class="text-[11px] text-slate-500 font-mono">{{ $e->slug }}</div>
                        </td>
                        <td class="p-3 text-xs text-slate-600 capitalize">{{ $e->surface }}</td>
                        <td class="p-3 text-xs">
                            @foreach($e->variants ?? [] as $v)
                                <span class="mr-1 rounded-full bg-slate-100 px-2 py-0.5 font-mono font-semibold">{{ $v['key'] }} · {{ $v['weight'] }}%</span>
                            @endforeach
                        </td>
                        <td class="p-3 font-mono">{{ number_format($imp) }}</td>
                        <td class="p-3 font-mono">{{ number_format($conv) }}
                            @if($imp > 0)<span class="ml-1 text-[11px] text-slate-500">({{ number_format($conv / $imp * 100, 1) }}%)</span>@endif
                        </td>
                        <td class="p-3">
                            <x-badge :tone="$e->status === 'running' ? 'green' : ($e->status === 'concluded' ? 'sky' : 'slate')">{{ $e->status }}</x-badge>
                        </td>
                        <td class="p-3 text-right">
                            <div class="flex justify-end gap-2">
                                <a href="{{ route('admin.ab.edit', $e) }}" class="btn-secondary !py-1 !text-xs">Edit</a>
                                <form method="POST" action="{{ route('admin.ab.destroy', $e) }}" data-confirm="Delete experiment?">@csrf @method('DELETE')<button class="btn-ghost !py-1 !text-xs !text-rose-600">Delete</button></form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="p-8 text-center text-sm text-slate-500">No experiments yet. <a href="{{ route('admin.ab.create') }}" class="text-violet-700 hover:underline">Create the first one →</a></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-6">{{ $experiments->links() }}</div>
</x-layouts.admin>
