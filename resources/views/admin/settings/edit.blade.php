<x-layouts.admin title="Platform settings">
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <h1 class="text-3xl font-black tracking-tight text-slate-900">Platform settings</h1>
            <p class="mt-1 text-sm text-slate-500">Fees, markup, escrow hold, signup rules. For AI provider + keys, see <a href="{{ route('admin.ai.edit') }}" class="text-violet-700 hover:underline">🤖 AI settings</a>.</p>
        </div>
    </div>

    @if(! empty($schemaMissing))
        <div class="mt-6 flex items-start gap-3 rounded-2xl border border-amber-300 bg-amber-50 p-4 text-sm text-amber-900">
            <span class="grid h-9 w-9 shrink-0 place-items-center rounded-xl bg-gradient-to-br from-amber-500 to-orange-500 text-white shadow-sm">⚠</span>
            <div class="flex-1"><p class="font-bold">platform_settings table not migrated yet.</p><p class="mt-1 text-xs text-amber-800">Run <code class="rounded bg-white/70 px-1.5 py-0.5">php artisan migrate</code>. Values below are defaults and can't be saved yet.</p></div>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.settings.update') }}" class="mt-8 grid gap-6 lg:grid-cols-2">
        @csrf

        <div class="rounded-2xl border border-slate-200 bg-white p-6">
            <h2 class="text-lg font-bold text-slate-900">Marketplace fees</h2>
            <div class="mt-4 grid gap-4">
                <div>
                    <label class="label">Paid campaigns platform fee (0.10 = 10%)</label>
                    <input class="input" type="number" step="0.0001" min="0" max="1" name="paid_platform_fee_rate" value="{{ old('paid_platform_fee_rate', $settings->paid_platform_fee_rate) }}">
                </div>
                <div>
                    <label class="label">Barter platform fee (0.05 = 5%)</label>
                    <input class="input" type="number" step="0.0001" min="0" max="1" name="barter_platform_fee_rate" value="{{ old('barter_platform_fee_rate', $settings->barter_platform_fee_rate) }}">
                </div>
                <div>
                    <label class="label">Processing markup (rate)</label>
                    <input class="input" type="number" step="0.0001" min="0" max="1" name="processing_markup_rate" value="{{ old('processing_markup_rate', $settings->processing_markup_rate) }}">
                </div>
                <div>
                    <label class="label">Processing markup (fixed ₹)</label>
                    <input class="input" type="number" step="0.01" min="0" name="processing_markup_fixed" value="{{ old('processing_markup_fixed', number_format(($settings->processing_markup_fixed_cents ?? 0)/100, 2, '.', '')) }}" placeholder="e.g. 0.30">
                </div>
            </div>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-6">
            <h2 class="text-lg font-bold text-slate-900">Escrow &amp; payouts</h2>
            <div class="mt-4 grid gap-4">
                <div>
                    <label class="label">Escrow hold days after approval</label>
                    <input class="input" type="number" min="0" max="60" name="escrow_hold_days" value="{{ old('escrow_hold_days', $settings->escrow_hold_days) }}">
                </div>
                <div>
                    <label class="label">Minimum payout (₹)</label>
                    <input class="input" type="number" step="0.01" min="0" name="minimum_payout" value="{{ old('minimum_payout', number_format(($settings->minimum_payout_cents ?? 0)/100, 2, '.', '')) }}" placeholder="e.g. 100.00">
                </div>
            </div>

            <h2 class="mt-8 text-lg font-bold text-slate-900">Signup rules</h2>
            <div class="mt-4 space-y-3">
                <label class="flex items-center gap-3 rounded-xl border border-slate-200 p-3">
                    <input type="hidden" name="allow_public_signup" value="0">
                    <input type="checkbox" name="allow_public_signup" value="1" @checked($settings->allow_public_signup) class="h-5 w-5 rounded border-slate-300 text-violet-600">
                    <div><div class="text-sm font-semibold">Allow public signup</div><div class="text-xs text-slate-500">If off, only invited users can create accounts.</div></div>
                </label>
                <label class="flex items-center gap-3 rounded-xl border border-slate-200 p-3">
                    <input type="hidden" name="require_creator_verification" value="0">
                    <input type="checkbox" name="require_creator_verification" value="1" @checked($settings->require_creator_verification) class="h-5 w-5 rounded border-slate-300 text-violet-600">
                    <div><div class="text-sm font-semibold">Require creator verification</div><div class="text-xs text-slate-500">New creators need admin approval before they appear in the marketplace.</div></div>
                </label>
            </div>
        </div>

        <div class="lg:col-span-2 flex justify-end">
            <button class="btn-primary">Save settings</button>
        </div>
    </form>
</x-layouts.admin>
