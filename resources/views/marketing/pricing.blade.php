<x-layouts.app panel="guest" title="Pricing">
    @include('marketing._hero', [
        'eyebrow' => 'Pricing · Free forever plan',
        'title'   => 'Simple pricing. <span class="text-gradient">No creator fees.</span>',
        'sub'     => 'Start free, upgrade when you want more campaigns or seats. We never take a cut from your creators.',
        'ctaText' => 'Start free',
    ])

    {{-- Toggle --}}
    <section class="mx-auto max-w-6xl px-4">
        <div class="reveal mx-auto mb-10 flex w-fit gap-1 rounded-full border border-slate-200 bg-white p-1 shadow-sm">
            <button class="tab-pill is-active" type="button">Monthly</button>
            <button class="tab-pill" type="button">Yearly · save 20%</button>
        </div>

        <div class="reveal grid gap-6 md:grid-cols-3">
            {{-- Free --}}
            <div class="card p-8">
                <h3 class="text-sm font-bold uppercase tracking-widest text-slate-500">Free</h3>
                <div class="mt-2 flex items-baseline gap-1">
                    <span class="text-5xl font-black text-slate-900">$0</span>
                    <span class="text-slate-500">/mo</span>
                </div>
                <p class="mt-2 text-sm text-slate-500">For teams testing creator marketing.</p>
                <a href="{{ route('register') }}" class="btn-secondary mt-6 w-full">Start free</a>
                <ul class="mt-8 space-y-3 text-sm">
                    @foreach(['5 campaigns / month', 'Unlimited creators in marketplace', 'AI briefs & matching', '1 seat', 'Community support'] as $f)
                        <li class="flex gap-2"><span class="text-emerald-500">✓</span> {{ $f }}</li>
                    @endforeach
                </ul>
            </div>

            {{-- Growth (featured) --}}
            <div class="relative overflow-hidden rounded-3xl p-1"
                 style="background-image: linear-gradient(140deg,#7c3aed,#ec4899,#f59e0b);">
                <div class="rounded-[calc(1.5rem-1px)] bg-white p-8">
                    <div class="flex items-center justify-between">
                        <h3 class="text-sm font-bold uppercase tracking-widest text-violet-700">Growth</h3>
                        <span class="badge badge-violet">Most popular</span>
                    </div>
                    <div class="mt-2 flex items-baseline gap-1">
                        <span class="text-5xl font-black text-slate-900">$149</span>
                        <span class="text-slate-500">/mo</span>
                    </div>
                    <p class="mt-2 text-sm text-slate-500">For DTC brands scaling seeding and UGC.</p>
                    <a href="{{ route('register') }}" class="btn-gradient mt-6 w-full">Start 14-day trial</a>
                    <ul class="mt-8 space-y-3 text-sm">
                        @foreach(['Unlimited campaigns', 'Bulk seeding automations', 'Shopify sync + attribution', '5 seats', 'AI content review', 'Priority support'] as $f)
                            <li class="flex gap-2"><span class="text-emerald-500">✓</span> {{ $f }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>

            {{-- Scale --}}
            <div class="card p-8">
                <h3 class="text-sm font-bold uppercase tracking-widest text-slate-500">Scale</h3>
                <div class="mt-2 flex items-baseline gap-1">
                    <span class="text-5xl font-black text-slate-900">$499</span>
                    <span class="text-slate-500">/mo</span>
                </div>
                <p class="mt-2 text-sm text-slate-500">For agencies and 8-figure DTC teams.</p>
                <a href="{{ route('contact') }}" class="btn-secondary mt-6 w-full">Talk to sales</a>
                <ul class="mt-8 space-y-3 text-sm">
                    @foreach(['Everything in Growth', 'Unlimited seats', 'Multi-brand workspaces', 'Dedicated CSM', 'Custom SLAs & SSO', 'API + Zapier'] as $f)
                        <li class="flex gap-2"><span class="text-emerald-500">✓</span> {{ $f }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    </section>

    {{-- Creator side --}}
    <section class="mx-auto mt-24 max-w-6xl px-4">
        <div class="reveal relative overflow-hidden rounded-3xl border border-slate-200 bg-white p-8 md:p-12">
            <div class="pointer-events-none absolute -right-10 top-0 h-64 w-64 rounded-full bg-gradient-to-br from-rose-300/40 to-orange-300/40 blur-3xl"></div>
            <div class="grid gap-8 md:grid-cols-2 md:items-center">
                <div>
                    <span class="chip"><span class="chip-dot"></span> Creators</span>
                    <h2 class="section-title mt-3 text-left">Always free for creators.</h2>
                    <p class="mt-3 text-slate-600">Zero platform fee. Zero commission on paid deals. We charge brands, not you.</p>
                    <a href="{{ route('register') }}" class="btn-gradient mt-6">Join as creator →</a>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div class="rounded-2xl bg-gradient-to-br from-rose-500 to-pink-500 p-5 text-white">
                        <div class="text-xs uppercase tracking-widest opacity-80">Platform fee</div>
                        <div class="mt-1 text-3xl font-black">0%</div>
                    </div>
                    <div class="rounded-2xl bg-slate-950 p-5 text-white">
                        <div class="text-xs uppercase tracking-widest opacity-80">Payout</div>
                        <div class="mt-1 text-3xl font-black">Net-7</div>
                    </div>
                    <div class="col-span-2 rounded-2xl bg-white ring-1 ring-slate-200 p-5">
                        <div class="text-xs uppercase tracking-widest text-slate-400">Included</div>
                        <ul class="mt-2 space-y-1 text-sm text-slate-700">
                            <li>• Verified profile + rate card</li>
                            <li>• Direct-to-brand messaging</li>
                            <li>• Auto contracts & receipts</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- FAQ --}}
    <section class="mx-auto max-w-3xl px-4 py-24">
        <div class="mx-auto max-w-2xl text-center">
            <p class="section-eyebrow reveal">FAQ</p>
            <h2 class="section-title reveal mt-3">Answers, quickly</h2>
        </div>
        <div class="reveal mt-10 divide-y divide-slate-200 rounded-2xl border border-slate-200 bg-white">
            @foreach([
                ['Is there really a free plan?', 'Yes. 5 campaigns per month, unlimited creators, AI briefs — all included at $0. No card required.'],
                ['Do you take a cut from creators?', 'Never. Creators keep 100% of paid deals and all product value from barter.'],
                ['Can I cancel anytime?', 'Yes. Downgrade or cancel from your dashboard with one click. No lock-in.'],
                ['Do I need a Shopify store?', 'Nope. CSV, WooCommerce, Amazon, or manual works too. Shopify just adds real-time order/inventory sync.'],
                ['How does attribution work?', 'Unique discount codes + referral links + multi-touch tracking. You see revenue tied to each creator.'],
            ] as $qa)
                <details class="group p-5">
                    <summary class="flex cursor-pointer items-center justify-between font-semibold text-slate-900">
                        {{ $qa[0] }}
                        <svg class="h-5 w-5 text-slate-400 transition group-open:rotate-180" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" d="M6 9l6 6 6-6"/></svg>
                    </summary>
                    <p class="mt-3 text-sm text-slate-600">{{ $qa[1] }}</p>
                </details>
            @endforeach
        </div>
    </section>

    @include('marketing._cta')
</x-layouts.app>
