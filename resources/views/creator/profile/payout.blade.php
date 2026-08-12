<x-layouts.app panel="creator" title="Payout details">
    <a href="{{ route('creator.profile.show') }}" class="text-sm text-slate-500">← Profile</a>
    <div class="mt-2 flex items-center gap-2">
        <h1 class="text-2xl font-black tracking-tight">💰 Payout details</h1>
        @if($creator->razorpayx_fund_account_id)
            <span class="badge-green">✓ Ready to receive</span>
        @endif
    </div>
    <p class="mt-1 text-sm text-slate-500">Money from brand-approved content is sent to your <strong>UPI</strong> (instant, free) or <strong>bank account</strong> (via IMPS/NEFT) after the escrow hold window.</p>

    <form method="POST" action="{{ route('creator.payout.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('PATCH')

        {{-- Method picker --}}
        <div class="grid gap-3 md:grid-cols-2">
            <label class="cursor-pointer rounded-2xl border-2 p-4 has-[:checked]:border-violet-500 has-[:checked]:bg-violet-50">
                <input type="radio" name="payout_method" value="upi" @checked($creator->payout_method === 'upi' || ! $creator->payout_method) class="sr-only">
                <p class="font-black">⚡ UPI <span class="ml-2 text-xs font-normal text-emerald-600">Instant · Free</span></p>
                <p class="mt-1 text-xs text-slate-500">Money arrives within seconds. Works with GPay, PhonePe, Paytm, BHIM, any UPI VPA.</p>
            </label>
            <label class="cursor-pointer rounded-2xl border-2 p-4 has-[:checked]:border-violet-500 has-[:checked]:bg-violet-50">
                <input type="radio" name="payout_method" value="bank" @checked($creator->payout_method === 'bank') class="sr-only">
                <p class="font-black">🏦 Bank account</p>
                <p class="mt-1 text-xs text-slate-500">IMPS / NEFT to any Indian bank. Arrives in minutes to a few hours.</p>
            </label>
        </div>

        {{-- UPI --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-5">
            <h2 class="text-sm font-black uppercase tracking-widest text-slate-500">UPI details</h2>
            <div class="mt-4">
                <label class="label">UPI VPA</label>
                <input class="input font-mono" name="upi_vpa" value="{{ old('upi_vpa', $creator->upi_vpa) }}" placeholder="yourname@paytm">
                <p class="mt-1 text-xs text-slate-500">Format: <code>yourname@bank</code> (e.g. <code>rahul@okhdfcbank</code>, <code>9876543210@paytm</code>)</p>
            </div>
        </div>

        {{-- Bank --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-5">
            <h2 class="text-sm font-black uppercase tracking-widest text-slate-500">Bank account details</h2>
            <div class="mt-4 grid gap-3 md:grid-cols-2">
                <div class="md:col-span-2">
                    <label class="label">Account holder name (as on bank record)</label>
                    <input class="input" name="bank_account_holder_name" value="{{ old('bank_account_holder_name', $creator->bank_account_holder_name) }}">
                </div>
                <div>
                    <label class="label">Account number</label>
                    <input class="input font-mono" name="bank_account_number" value="{{ old('bank_account_number', $creator->bank_account_number) }}">
                </div>
                <div>
                    <label class="label">IFSC code</label>
                    <input class="input font-mono uppercase" name="bank_ifsc" value="{{ old('bank_ifsc', $creator->bank_ifsc) }}" placeholder="HDFC0000123">
                </div>
            </div>
        </div>

        {{-- KYC --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-5">
            <h2 class="text-sm font-black uppercase tracking-widest text-slate-500">Tax (PAN)</h2>
            <div class="mt-4">
                <label class="label">PAN number</label>
                <input class="input font-mono uppercase" name="pan_number" value="{{ old('pan_number', $creator->pan_number) }}" placeholder="ABCDE1234F" maxlength="10">
                <p class="mt-1 text-xs text-slate-500">Required for payouts &gt; ₹20,000 per financial year (TDS compliance).</p>
            </div>
        </div>

        <div class="flex justify-end gap-2">
            <a href="{{ route('creator.profile.show') }}" class="btn-ghost">Cancel</a>
            <button class="btn-gradient">💾 Save payout details</button>
        </div>
    </form>

    <div class="mt-6 rounded-2xl border border-emerald-100 bg-emerald-50 p-4 text-xs text-emerald-900">
        <p class="font-bold">🔒 How we keep your money safe</p>
        <p class="mt-1">All bank/UPI details are encrypted at rest. CreatorPlex uses <strong>RazorpayX</strong> (RBI-regulated) to move funds — we never touch or hold your money, it goes brand → RazorpayX → you.</p>
    </div>
</x-layouts.app>
