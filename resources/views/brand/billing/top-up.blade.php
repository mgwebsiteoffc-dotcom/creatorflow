<x-layouts.app panel="brand" title="Top up escrow">
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <a href="{{ route('brand.billing.index') }}" class="text-sm text-slate-500">← Billing</a>
            <h1 class="mt-1 text-2xl font-black tracking-tight text-slate-900 md:text-3xl">Top up escrow</h1>
            <p class="mt-1 text-sm text-slate-500">Add funds to your escrow balance. Creators are paid out from here automatically once their content is approved.</p>
        </div>
        <div class="rounded-2xl border border-slate-200 bg-white p-4 text-right">
            <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Available escrow</p>
            <p class="mt-1 text-2xl font-black text-emerald-600 tabular-nums">
                {{ $workspace->formatMoney($balanceCents) }}
            </p>
        </div>
    </div>

    <div class="mt-5 grid gap-4 lg:grid-cols-3">
        {{-- Top-up form (left) --}}
        <div class="lg:col-span-2 rounded-2xl border border-slate-200 bg-white p-5">
            <h2 class="text-sm font-bold text-slate-900">Add funds</h2>
            <p class="mt-1 text-xs text-slate-500">Minimum ₹100 · Maximum ₹10,00,000 per transaction.</p>

            {{-- Quick preset amounts --}}
            <div class="mt-4 grid grid-cols-3 gap-2 sm:grid-cols-6">
                @foreach([1000, 5000, 10000, 25000, 50000, 100000] as $preset)
                    <button type="button" data-preset="{{ $preset }}"
                            class="rounded-lg border border-slate-200 bg-white px-3 py-2 text-xs font-semibold text-slate-700 hover:border-violet-300 hover:bg-violet-50 hover:text-violet-700">
                        ₹{{ number_format($preset) }}
                    </button>
                @endforeach
            </div>

            <div class="mt-4">
                <label class="label">Amount (₹) <span class="text-rose-500">*</span></label>
                <input id="topup-amount" class="input !text-lg !py-3 font-semibold tabular-nums" type="number" step="1" min="100" max="1000000" placeholder="10,000">
                <p id="topup-error" class="mt-1 hidden text-xs font-semibold text-rose-600"></p>
            </div>

            @if($razorpayReady)
                <button id="topup-pay" class="btn-primary mt-5 w-full !py-3 text-sm">
                    <x-icon name="lock" class="h-4 w-4" />
                    Pay securely with Razorpay
                </button>
                <p class="mt-2 text-center text-[11px] text-slate-500">
                    Payment processed by Razorpay · {{ $razorpayIsLive ? 'Live mode' : 'Test mode' }}
                </p>
            @else
                <div class="mt-5 rounded-xl border border-amber-200 bg-amber-50 p-3 text-xs text-amber-900">
                    Razorpay isn't configured yet. You can still add funds manually and reconcile later.
                </div>
                <form method="POST" action="{{ route('brand.escrow.top-up.manual') }}" class="mt-3">
                    @csrf
                    <input type="hidden" name="amount" id="manual-amount">
                    <input class="input mb-2" name="note" placeholder="Note (optional) — e.g. NEFT ref #12345">
                    <button id="topup-manual" class="btn-secondary w-full">Record manual top-up</button>
                </form>
            @endif
        </div>

        {{-- Recent activity (right) --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-5">
            <h2 class="text-sm font-bold text-slate-900">Recent activity</h2>
            @if($recentTx->isEmpty())
                <p class="mt-3 text-xs text-slate-500">No escrow movements yet. Add funds to get started.</p>
            @else
                <ul class="mt-3 divide-y divide-slate-100">
                    @foreach($recentTx as $tx)
                        @php
                            $isIn = in_array($tx->kind, ['hold', 'adjustment']);
                            $color = $isIn ? 'text-emerald-600' : 'text-rose-600';
                            $sign  = $isIn ? '+' : '−';
                        @endphp
                        <li class="flex items-center justify-between py-2.5">
                            <div class="min-w-0">
                                <p class="truncate text-xs font-semibold capitalize text-slate-900">{{ $tx->kind }}</p>
                                <p class="truncate text-[11px] text-slate-500">{{ $tx->note ?: $tx->reference ?: 'System' }} · {{ $tx->created_at?->diffForHumans() }}</p>
                            </div>
                            <span class="shrink-0 text-sm font-black tabular-nums {{ $color }}">
                                {{ $sign }}{{ $workspace->formatMoney($tx->amount_cents) }}
                            </span>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>

    @if($razorpayReady)
        <script src="https://checkout.razorpay.com/v1/checkout.js" defer></script>
    @endif
    <script>
    (function () {
        const amount  = document.getElementById('topup-amount');
        const err     = document.getElementById('topup-error');
        const payBtn  = document.getElementById('topup-pay');
        const manual  = document.getElementById('topup-manual');
        const manualAmount = document.getElementById('manual-amount');
        const csrf    = document.querySelector('meta[name="csrf-token"]')?.content || '';

        document.querySelectorAll('[data-preset]').forEach(btn => {
            btn.addEventListener('click', () => {
                amount.value = btn.dataset.preset;
                if (manualAmount) manualAmount.value = btn.dataset.preset;
                err?.classList.add('hidden');
            });
        });
        amount?.addEventListener('input', () => {
            if (manualAmount) manualAmount.value = amount.value;
        });

        const showError = (msg) => { err.textContent = msg; err.classList.remove('hidden'); };

        payBtn?.addEventListener('click', async () => {
            const value = parseFloat(amount.value);
            if (! value || value < 100) return showError('Enter at least ₹100.');
            if (value > 1000000) return showError('Maximum per transaction is ₹10,00,000.');

            payBtn.disabled = true;
            payBtn.textContent = 'Preparing…';

            try {
                const res = await fetch(@json(route('brand.escrow.top-up.create')), {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
                    body: JSON.stringify({ amount: value }),
                });
                const body = await res.json();
                if (! res.ok || ! body.ok) throw new Error(body.error || 'Could not start payment.');

                const rzp = new Razorpay({
                    key: body.razorpay_key,
                    order_id: body.order_id,
                    amount: body.amount_cents,
                    currency: body.currency,
                    name: body.brand_name || 'CreatorPlex',
                    description: body.workspace ? `Escrow top-up — ${body.workspace}` : 'Escrow top-up',
                    prefill: body.prefill,
                    theme: { color: '#7c3aed' },
                    modal: { ondismiss: () => { payBtn.disabled = false; payBtn.textContent = '🔒 Pay securely with Razorpay'; } },
                    handler: async (resp) => {
                        try {
                            const confirm = await fetch(@json(route('brand.escrow.top-up.confirm')), {
                                method: 'POST',
                                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
                                body: JSON.stringify({
                                    razorpay_order_id:   resp.razorpay_order_id,
                                    razorpay_payment_id: resp.razorpay_payment_id,
                                    razorpay_signature:  resp.razorpay_signature,
                                    amount_cents:        body.amount_cents,
                                }),
                            });
                            const cbody = await confirm.json();
                            if (! confirm.ok || ! cbody.ok) throw new Error(cbody.error || 'Verification failed.');
                            window.location.assign(cbody.next);
                        } catch (e) {
                            showError(e.message || 'Payment succeeded but verification failed. Please contact support with your reference.');
                            payBtn.disabled = false;
                            payBtn.textContent = 'Try again';
                        }
                    },
                });
                rzp.open();
            } catch (e) {
                showError(e.message || 'Something went wrong.');
                payBtn.disabled = false;
                payBtn.textContent = 'Pay securely with Razorpay';
            }
        });

        // Manual path (no Razorpay) — copy the amount into the hidden input on submit.
        manual?.closest('form')?.addEventListener('submit', () => {
            if (manualAmount && amount) manualAmount.value = amount.value;
        });
    })();
    </script>
</x-layouts.app>
