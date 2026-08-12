<x-layouts.admin title="Referrals & affiliates">
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <p class="text-xs font-bold uppercase tracking-widest text-violet-600">Superadmin · Growth</p>
            <h1 class="mt-1 text-3xl font-black tracking-tight text-slate-900">Referrals &amp; affiliates</h1>
            <p class="mt-1 text-sm text-slate-500">Create codes for creators (creator affiliate) and brands (refer-a-brand). Track signups + conversions + commission.</p>
        </div>
    </div>

    {{-- KPIs --}}
    <div class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-5">
        <div class="rounded-2xl bg-gradient-to-br from-violet-500 to-pink-500 p-5 text-white shadow-lg"><p class="text-[10px] font-bold uppercase tracking-widest opacity-90">Total codes</p><p class="mt-2 text-3xl font-black">{{ number_format($stats['total_codes']) }}</p></div>
        <div class="rounded-2xl bg-gradient-to-br from-emerald-500 to-teal-500 p-5 text-white shadow-lg"><p class="text-[10px] font-bold uppercase tracking-widest opacity-90">Active</p><p class="mt-2 text-3xl font-black">{{ number_format($stats['active_codes']) }}</p></div>
        <div class="rounded-2xl bg-gradient-to-br from-amber-500 to-orange-500 p-5 text-white shadow-lg"><p class="text-[10px] font-bold uppercase tracking-widest opacity-90">Signups</p><p class="mt-2 text-3xl font-black">{{ number_format($stats['total_signups']) }}</p></div>
        <div class="rounded-2xl bg-gradient-to-br from-sky-500 to-indigo-500 p-5 text-white shadow-lg"><p class="text-[10px] font-bold uppercase tracking-widest opacity-90">Conversions</p><p class="mt-2 text-3xl font-black">{{ number_format($stats['total_conversions']) }}</p></div>
        <div class="rounded-2xl bg-gradient-to-br from-slate-700 to-slate-900 p-5 text-white shadow-lg"><p class="text-[10px] font-bold uppercase tracking-widest opacity-90">Commission</p><p class="mt-2 text-3xl font-black">₹{{ number_format($stats['total_commission']/100, 0, '.', ',') }}</p></div>
    </div>

    {{-- Create form --}}
    <section class="mt-8 rounded-2xl border border-slate-200 bg-white p-6">
        <h2 class="text-lg font-black text-slate-900">+ New referral code</h2>
        <form method="POST" action="{{ route('admin.referrals.store') }}" class="mt-4 grid gap-3 md:grid-cols-6">
            @csrf
            <div><label class="label">Kind</label>
                <select class="input" name="kind">
                    <option value="brand_referral">Brand referral</option>
                    <option value="creator_affiliate">Creator affiliate</option>
                    <option value="partner">Agency / partner</option>
                </select>
            </div>
            <div><label class="label">Owner type</label>
                <select class="input" name="owner_type">
                    <option value="creator">Creator</option>
                    <option value="user">User (brand)</option>
                    <option value="agency">Agency</option>
                </select>
            </div>
            <div><label class="label">Owner ID</label><input class="input" name="owner_id" type="number" required></div>
            <div><label class="label">Code (blank = auto)</label><input class="input font-mono uppercase" name="code" placeholder="RIYA10"></div>
            <div><label class="label">Commission %</label><input class="input" name="commission_rate" type="number" step="0.1" value="20"></div>
            <div><label class="label">Fixed bonus (₹)</label><input class="input" name="commission_fixed" type="number" step="0.01" min="0" value="0" placeholder="e.g. 500.00"></div>
            <div class="md:col-span-6"><label class="label">Label (optional)</label><input class="input" name="label"></div>
            <div class="md:col-span-6 flex justify-end"><button class="btn-primary">Create code</button></div>
        </form>
    </section>

    {{-- Codes table --}}
    <div class="mt-6 overflow-x-auto rounded-2xl border border-slate-200 bg-white">
        <table class="w-full min-w-[640px] text-sm">
            <thead class="border-b border-slate-200 bg-slate-50 text-left text-xs uppercase tracking-widest text-slate-500">
                <tr><th class="p-3">Code</th><th class="p-3">Kind</th><th class="p-3">Owner</th><th class="p-3">Commission</th><th class="p-3">Signups</th><th class="p-3">Conversions</th><th class="p-3">Status</th><th class="p-3 text-right">Actions</th></tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($codes as $c)
                    <tr>
                        <td class="p-3 font-mono font-bold text-slate-900">{{ $c->code }}</td>
                        <td class="p-3 text-xs"><span class="rounded-full bg-slate-100 px-2 py-0.5 capitalize">{{ str_replace('_',' ', $c->kind) }}</span></td>
                        <td class="p-3 text-xs text-slate-500">{{ ucfirst($c->owner_type) }} #{{ $c->owner_id }}</td>
                        <td class="p-3 text-xs">{{ $c->commission_rate }}%{{ $c->commission_fixed_cents >0 ? ' + ₹'.number_format($c->commission_fixed_cents/100, 2, '.', ',') : '' }}</td>
                        <td class="p-3 font-mono">{{ number_format($c->signup_count) }}</td>
                        <td class="p-3 font-mono">{{ number_format($c->conversion_count) }}</td>
                        <td class="p-3"><x-badge :tone="$c->status === 'active' ? 'green' : 'slate'">{{ $c->status }}</x-badge></td>
                        <td class="p-3 text-right">
                            <div class="flex justify-end gap-2">
                                <form method="POST" action="{{ route('admin.referrals.toggle', $c) }}">@csrf @method('PATCH')<button class="btn-secondary !py-1 !text-xs">{{ $c->status === 'active' ? 'Disable' : 'Enable' }}</button></form>
                                <form method="POST" action="{{ route('admin.referrals.destroy', $c) }}" data-confirm="Delete code?">@csrf @method('DELETE')<button class="btn-ghost !py-1 !text-xs !text-rose-600">Delete</button></form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="p-8 text-center text-sm text-slate-500">No codes yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-6">{{ $codes->links() }}</div>
</x-layouts.admin>
