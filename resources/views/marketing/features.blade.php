<x-layouts.app panel="guest" title="Features">
    @include('marketing._hero', [
        'eyebrow' => 'Features · Built for creator commerce',
        'title'   => 'Every tool your <span class="text-gradient">campaign engine</span> needs',
        'sub'     => 'From product to payout — one platform for brands and creators, powered by AI and native Shopify sync.',
    ])

    {{-- Feature blocks (alternating) --}}
    <section class="mx-auto max-w-6xl space-y-24 px-4 pb-24">
        @php
            $features = [
                [
                    'eyebrow' => 'AI CAMPAIGN ENGINE',
                    'title'   => 'Launch-ready campaigns in one click',
                    'body'    => 'Connect your catalog and AI writes the brief, picks hero products, estimates reach and ROI, and shortlists creators — all in under a minute.',
                    'bullets' => ['12+ targeting parameters', 'Predicted reach, ER, ROAS', 'Auto-drafted deliverables', 'Editable before launch'],
                    'grad'    => 'from-violet-500 to-pink-500',
                    'icon'    => '✨',
                ],
                [
                    'eyebrow' => 'BULK SEEDING · NATIVE',
                    'title'   => 'Seed 100s of creators without breaking a sweat',
                    'body'    => 'Product A → 100 creators, B → 50. Automatic Shopify orders, tracking, waitlists, and acceptance rate handling. Zero spreadsheet ops.',
                    'bullets' => ['Waitlists + acceptance flow', 'Automatic Shopify orders', 'Real-time tracking sync', 'Cost per accepted seat'],
                    'grad'    => 'from-cyan-500 to-emerald-500',
                    'icon'    => '📦',
                ],
                [
                    'eyebrow' => 'SHOPIFY-GRADE SYNC',
                    'title'   => 'Products, orders, and inventory — always in sync',
                    'body'    => 'Two-way sync via webhooks and nightly reconcile. Discounts, collections, images, variants — all mirrored, always fresh.',
                    'bullets' => ['Webhook + nightly reconcile', 'Products / inventory / images', 'Discounts + collections', 'CSV, Woo, Amazon supported'],
                    'grad'    => 'from-emerald-500 to-teal-500',
                    'icon'    => '🛍️',
                ],
                [
                    'eyebrow' => 'CONTENT REVIEW',
                    'title'   => 'AI-assisted content approval that scales',
                    'body'    => 'Creators upload from a mobile PWA. AI scores brand fit, checks disclosures, and flags issues before you spend a second reviewing.',
                    'bullets' => ['Mobile PWA uploads', 'AI brand-fit score', 'Change requests + threads', 'Usage rights & contracts'],
                    'grad'    => 'from-fuchsia-500 to-purple-600',
                    'icon'    => '🎬',
                ],
                [
                    'eyebrow' => 'REAL ATTRIBUTION',
                    'title'   => 'Tie every creator to actual revenue',
                    'body'    => 'Unique discount codes, referral links, and multi-touch attribution. Know which creator moved which dollar — not just impressions.',
                    'bullets' => ['Unique codes per creator', 'UTM + referral tracking', 'Multi-touch attribution', 'Roll-up ROAS by campaign'],
                    'grad'    => 'from-amber-500 to-rose-500',
                    'icon'    => '💰',
                ],
            ];
        @endphp

        @foreach($features as $i => $f)
            <div class="grid gap-10 md:grid-cols-2 md:items-center {{ $i % 2 ? 'md:[&>*:first-child]:order-last' : '' }}">
                <div class="reveal">
                    <span class="section-eyebrow">{{ $f['eyebrow'] }}</span>
                    <h2 class="section-title mt-2 text-left">{{ $f['title'] }}</h2>
                    <p class="mt-4 text-slate-600">{{ $f['body'] }}</p>
                    <ul class="mt-6 grid gap-3 sm:grid-cols-2">
                        @foreach($f['bullets'] as $b)
                            <li class="flex items-start gap-2 text-sm text-slate-700">
                                <span class="mt-0.5 grid h-5 w-5 shrink-0 place-items-center rounded-full bg-gradient-to-br {{ $f['grad'] }} text-[11px] font-black text-white">✓</span>
                                {{ $b }}
                            </li>
                        @endforeach
                    </ul>
                </div>

                <div class="reveal">
                    <div class="g-border p-1 shadow-xl">
                        <div class="rounded-[calc(1.25rem-1px)] bg-white p-6">
                            <div class="flex items-center justify-between">
                                <span class="grid h-10 w-10 place-items-center rounded-xl bg-gradient-to-br {{ $f['grad'] }} text-lg text-white">{{ $f['icon'] }}</span>
                                <span class="badge badge-violet">Live</span>
                            </div>
                            <div class="mt-6 h-40 overflow-hidden rounded-xl bg-gradient-to-br {{ $f['grad'] }} p-5 text-white">
                                <div class="text-xs uppercase tracking-widest opacity-80">{{ $f['eyebrow'] }}</div>
                                <div class="mt-2 text-2xl font-black leading-tight">{{ $f['title'] }}</div>
                            </div>
                            <div class="mt-4 grid grid-cols-3 gap-2 text-center">
                                @foreach(['Setup', 'Live', 'Impact'] as $j => $m)
                                    <div class="rounded-lg bg-slate-50 p-3">
                                        <div class="text-[10px] uppercase tracking-wider text-slate-400">{{ $m }}</div>
                                        <div class="text-sm font-bold text-slate-900">{{ ['3 min', '24 h', '10×'][$j] }}</div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </section>

    {{-- Compare --}}
    <section class="mx-auto max-w-6xl px-4 pb-24">
        <div class="mx-auto max-w-2xl text-center">
            <p class="section-eyebrow reveal">All-in-one</p>
            <h2 class="section-title reveal mt-3">Replace 5+ tools with one platform</h2>
        </div>

        <div class="reveal mt-10 overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
            <div class="grid grid-cols-3 border-b border-slate-200 bg-slate-50 text-sm font-semibold text-slate-500">
                <div class="p-4">You currently use</div>
                <div class="p-4 text-center">Job to be done</div>
                <div class="bg-gradient-to-r from-violet-600 to-pink-600 p-4 text-center text-white">CreatorFlow</div>
            </div>
            @foreach([
                ['Notion + Google Sheets', 'Campaign planning',   '✅'],
                ['Aspire / GRIN',          'Creator marketplace', '✅'],
                ['Google Drive',           'Content review',      '✅'],
                ['Klaviyo + tags',         'Attribution',         '✅'],
                ['Manual DMs',             'Creator outreach',    '✅'],
                ['Loop / Shopify apps',    'Bulk seeding',        '✅'],
            ] as $row)
                <div class="grid grid-cols-3 border-b border-slate-100 text-sm last:border-0">
                    <div class="p-4 text-slate-500 line-through">{{ $row[0] }}</div>
                    <div class="p-4 text-center font-medium text-slate-700">{{ $row[1] }}</div>
                    <div class="bg-violet-50/40 p-4 text-center text-lg">{{ $row[2] }}</div>
                </div>
            @endforeach
        </div>
    </section>

    @include('marketing._cta')
</x-layouts.app>
