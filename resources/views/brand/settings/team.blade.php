<x-layouts.app panel="brand" title="Team">
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <h1 class="text-3xl font-black tracking-tight text-slate-900">Team</h1>
            <p class="mt-1 text-sm text-slate-500">People with access to <strong>{{ $workspace->name }}</strong>.</p>
        </div>
    </div>

    <div class="mt-6 flex flex-wrap gap-2">
        <a href="{{ route('brand.settings.profile') }}" class="tab-pill">🏢 Profile</a>
        <a href="{{ route('brand.settings.team') }}"    class="tab-pill is-active">👥 Team</a>
        <a href="{{ route('brand.billing.index') }}"    class="tab-pill">💳 Billing</a>
    </div>

    <div class="mt-8 overflow-hidden rounded-2xl border border-slate-200 bg-white">
        <table class="w-full text-sm">
            <thead class="border-b border-slate-200 bg-slate-50 text-left text-xs uppercase tracking-widest text-slate-500">
                <tr><th class="p-3">Member</th><th class="p-3">Role</th><th class="p-3">Joined</th></tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @foreach($workspace->users as $u)
                    <tr>
                        <td class="p-3">
                            <div class="flex items-center gap-3">
                                <div class="grid h-9 w-9 place-items-center rounded-full bg-slate-100 font-bold text-slate-500">{{ strtoupper(substr($u->name,0,1)) }}</div>
                                <div>
                                    <div class="font-semibold text-slate-900">{{ $u->name }}</div>
                                    <div class="text-xs text-slate-500">{{ $u->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="p-3 capitalize"><x-badge tone="violet">{{ $u->pivot->role }}</x-badge></td>
                        <td class="p-3 text-slate-500">{{ optional($u->pivot->accepted_at)->format('M j, Y') ?: '—' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <p class="mt-4 text-xs text-slate-500">Team invitations open on <strong>Growth</strong> and <strong>Scale</strong> plans.</p>
</x-layouts.app>
