<x-layouts.admin title="New agency">
    <a href="{{ route('admin.agencies.index') }}" class="text-sm text-slate-500 hover:text-slate-800">← Agencies</a>
    <h1 class="mt-1 text-3xl font-black tracking-tight text-slate-900">New agency</h1>
    <p class="mt-1 text-sm text-slate-500">An agency umbrella can group multiple brand workspaces so one agency owner sees everything in aggregate.</p>

    <form method="POST" action="{{ route('admin.agencies.store') }}" class="mt-6 grid gap-6 lg:grid-cols-3">
        @csrf
        <div class="lg:col-span-2 rounded-2xl border border-slate-200 bg-white p-6">
            <h2 class="text-lg font-bold text-slate-900">Basics</h2>
            <div class="mt-4 grid gap-4 md:grid-cols-2">
                <div class="md:col-span-2">
                    <label class="label">Agency name</label>
                    <input class="input" name="name" required placeholder="Rank Booster Infotech">
                </div>
                <div>
                    <label class="label">Plan</label>
                    <select class="input" name="plan">
                        <option value="agency">Agency (default)</option>
                        <option value="agency_pro">Agency Pro</option>
                        <option value="enterprise">Enterprise</option>
                    </select>
                </div>
                <div>
                    <label class="label">Owner email (optional)</label>
                    <input class="input" type="email" name="owner_email" placeholder="owner@agency.com">
                    <p class="mt-1 text-xs text-slate-500">We'll create the user account if it doesn't exist.</p>
                </div>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-6">
            <h2 class="text-lg font-bold text-slate-900">Attach brands (optional)</h2>
            <p class="mt-1 text-xs text-slate-500">Pick unassigned workspaces to attach right now. You can attach more later.</p>
            <div class="mt-4 max-h-72 space-y-2 overflow-y-auto">
                @forelse($unassignedWorkspaces as $w)
                    <label class="flex items-start gap-2 rounded-xl border border-slate-100 p-3 hover:border-violet-300">
                        <input type="checkbox" name="workspace_ids[]" value="{{ $w->id }}" class="mt-0.5">
                        <div class="min-w-0 flex-1">
                            <div class="truncate text-sm font-semibold text-slate-900">{{ $w->name }}</div>
                            <div class="text-xs text-slate-500">{{ $w->website ?: '—' }}</div>
                        </div>
                    </label>
                @empty
                    <p class="text-xs text-slate-500">No unassigned workspaces.</p>
                @endforelse
            </div>
        </div>

        <div class="lg:col-span-3 flex justify-end">
            <button class="btn-gradient">Create agency</button>
        </div>
    </form>
</x-layouts.admin>
