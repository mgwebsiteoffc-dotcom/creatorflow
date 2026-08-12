<x-layouts.app panel="brand" title="Get started">
    <div class="mx-auto max-w-4xl">
        <div class="mb-2 flex items-center gap-2 text-xs font-semibold uppercase tracking-widest text-violet-700">
            <span class="chip-dot"></span> Brand onboarding
        </div>
        <h1 class="text-3xl font-black tracking-tight text-slate-900 sm:text-4xl">
            Let's get you <span class="text-gradient">campaign-ready</span>
        </h1>
        <p class="mt-2 text-sm text-slate-500">
            <span data-progress>Step 1 of 4</span> · Takes about 3 minutes. You can edit anything later.
        </p>

        <div data-wizard class="mt-8">
            {{-- STEPPER --}}
            <div class="mb-8 flex items-center gap-2">
                @foreach([1,2,3,4] as $i)
                    <button type="button" data-jump="{{ $i }}" data-dot="{{ $i }}" class="wizard-dot" aria-label="Step {{ $i }}">{{ $i }}</button>
                    @if($i < 4)<div data-bar="{{ $i }}" class="wizard-bar"><span></span></div>@endif
                @endforeach
            </div>

            <div class="g-border p-1">
                <div class="rounded-[calc(1.25rem-1px)] bg-white p-6 md:p-8">

                    {{-- STEP 1: Choose product source --}}
                    <div data-step="1" class="wizard-step">
                        <div class="mb-6">
                            <h2 class="text-xl font-bold text-slate-900">How do you want to bring products in?</h2>
                            <p class="mt-1 text-sm text-slate-500">Pick any one. You can add more sources later.</p>
                        </div>

                        <div class="grid gap-4 md:grid-cols-3">
                            {{-- Shopify --}}
                            <div class="card card-hover flex flex-col p-5">
                                <div class="grid h-11 w-11 place-items-center rounded-xl bg-gradient-to-br from-emerald-400 to-teal-500 text-lg text-white">🛍️</div>
                                <h3 class="mt-3 font-bold text-slate-900">Connect Shopify</h3>
                                <p class="mt-1 text-xs text-slate-500">Auto-sync products, inventory, images, and orders in real time.</p>
                                <form method="POST" action="{{ route('brand.onboarding.shopify') }}" class="mt-4">
                                    @csrf
                                    <button class="btn-primary w-full" {{ $shopifyConnected ? 'disabled' : '' }}>
                                        {{ $shopifyConnected ? '✓ Connected' : 'Install app' }}
                                    </button>
                                </form>
                            </div>

                            {{-- CSV --}}
                            <div class="card card-hover flex flex-col p-5">
                                <div class="grid h-11 w-11 place-items-center rounded-xl bg-gradient-to-br from-violet-500 to-indigo-500 text-lg text-white">📄</div>
                                <h3 class="mt-3 font-bold text-slate-900">Upload CSV</h3>
                                <p class="mt-1 text-xs text-slate-500">From WooCommerce, Amazon, or a spreadsheet. We map the columns.</p>
                                <a href="{{ route('brand.products.import') }}" class="btn-secondary mt-4 w-full">Import CSV</a>
                            </div>

                            {{-- Manual --}}
                            <div class="card card-hover flex flex-col p-5">
                                <div class="grid h-11 w-11 place-items-center rounded-xl bg-gradient-to-br from-pink-500 to-rose-500 text-lg text-white">➕</div>
                                <h3 class="mt-3 font-bold text-slate-900">Add manually</h3>
                                <p class="mt-1 text-xs text-slate-500">Quick single-product setup — perfect for launches.</p>
                                <button type="button" data-next class="btn-secondary mt-4 w-full">Add a product →</button>
                            </div>
                        </div>

                        <div class="mt-8 flex flex-wrap items-center justify-between gap-3 border-t border-slate-200 pt-6">
                            <p class="text-xs text-slate-500">You'll be able to add more products later.</p>
                            <button type="button" data-next class="btn-gradient">Next: Add a product →</button>
                        </div>
                    </div>

                    {{-- STEP 2: Add a manual product --}}
                    <div data-step="2" class="wizard-step">
                        <div class="mb-6">
                            <h2 class="text-xl font-bold text-slate-900">Add your first product</h2>
                            <p class="mt-1 text-sm text-slate-500">This becomes the seed for your first campaign. You can skip if you already have products.</p>
                        </div>

                        <form method="POST" action="{{ route('brand.onboarding.product') }}" class="space-y-4">
                            @csrf
                            <div>
                                <label class="label">Product title</label>
                                <input class="input" name="title" placeholder="e.g. Glow Serum 30ml" value="{{ old('title') }}">
                            </div>
                            <div>
                                <label class="label">Short description</label>
                                <textarea class="input min-h-24" name="description" placeholder="Who is it for, what makes it special?">{{ old('description') }}</textarea>
                            </div>
                            <div class="grid gap-4 sm:grid-cols-3">
                                <div>
                                    <label class="label">Category</label>
                                    <input class="input" name="product_type" placeholder="Skincare" value="{{ old('product_type') }}">
                                </div>
                                <div>
                                    <label class="label">Price (in cents)</label>
                                    <input class="input" type="number" min="0" name="price_cents" placeholder="49900 = ₹499" value="{{ old('price_cents') }}">
                                </div>
                                <div>
                                    <label class="label">Inventory</label>
                                    <input class="input" type="number" min="0" name="inventory_qty" placeholder="100" value="{{ old('inventory_qty') }}">
                                </div>
                            </div>
                            <div class="flex flex-wrap items-center justify-between gap-3 border-t border-slate-200 pt-6">
                                <button type="button" data-prev class="btn-ghost">← Back</button>
                                <div class="flex gap-2">
                                    <button type="button" data-next class="btn-secondary">Skip for now</button>
                                    <button type="submit" class="btn-gradient">Save &amp; continue →</button>
                                </div>
                            </div>
                        </form>
                    </div>

                    {{-- STEP 3: AI analyze --}}
                    <div data-step="3" class="wizard-step">
                        <div class="mb-6">
                            <h2 class="text-xl font-bold text-slate-900">Let AI understand your catalog</h2>
                            <p class="mt-1 text-sm text-slate-500">We'll detect your niche, hero products, content angles, and a starter creator pool.</p>
                        </div>

                        <div class="grid gap-4 md:grid-cols-3">
                            @foreach([
                                ['🧠', 'Niche detection', 'Cluster products into buyer intents & niches.'],
                                ['⭐', 'Hero products', 'Rank the top 5 candidates for seeding.'],
                                ['🎯', 'Creator match', 'Score 100K+ creators against your catalog.'],
                            ] as $step)
                                <div class="card p-4">
                                    <div class="text-xl">{{ $step[0] }}</div>
                                    <div class="mt-2 text-sm font-bold text-slate-900">{{ $step[1] }}</div>
                                    <div class="mt-1 text-xs text-slate-500">{{ $step[2] }}</div>
                                </div>
                            @endforeach
                        </div>

                        @if($productsCount > 0)
                            <form method="POST" action="{{ route('brand.onboarding.analyze') }}" class="mt-6">
                                @csrf
                                <div class="flex flex-wrap items-center justify-between gap-3 rounded-2xl bg-gradient-to-r from-violet-50 to-cyan-50 p-5">
                                    <div>
                                        <div class="text-sm font-bold text-slate-900">{{ $productsCount }} products ready</div>
                                        <div class="text-xs text-slate-500">Runs in about 20 seconds.</div>
                                    </div>
                                    <button class="btn-gradient">✨ Run AI analysis</button>
                                </div>
                            </form>
                        @else
                            <div class="mt-6 rounded-2xl border border-dashed border-amber-300 bg-amber-50 p-5 text-sm text-amber-800">
                                Add at least one product first, or connect Shopify to auto-import.
                            </div>
                        @endif

                        <div class="mt-8 flex flex-wrap items-center justify-between gap-3 border-t border-slate-200 pt-6">
                            <button type="button" data-prev class="btn-ghost">← Back</button>
                            <button type="button" data-next class="btn-gradient">Next: Preferences →</button>
                        </div>
                    </div>

                    {{-- STEP 4: Complete --}}
                    <div data-step="4" class="wizard-step">
                        <div class="mb-6">
                            <h2 class="text-xl font-bold text-slate-900">Campaign preferences</h2>
                            <p class="mt-1 text-sm text-slate-500">Optional. You can change these anytime.</p>
                        </div>

                        <form method="POST" action="{{ route('brand.onboarding.complete') }}" class="space-y-6">
                            @csrf

                            <div>
                                <label class="label">What outcome matters most?</label>
                                <div class="grid gap-3 sm:grid-cols-3">
                                    @foreach([
                                        ['awareness', '📣', 'Awareness', 'Reach + impressions'],
                                        ['ugc', '🎬', 'UGC volume', 'Content library for ads'],
                                        ['sales', '💰', 'Sales', 'Direct attributed revenue'],
                                    ] as $goal)
                                        <label class="cursor-pointer">
                                            <input type="radio" name="primary_goal" value="{{ $goal[0] }}" class="sr-only" {{ $loop->first ? 'checked' : '' }}>
                                            <div class="pick-tile">
                                                <div class="text-2xl">{{ $goal[1] }}</div>
                                                <div class="mt-1 font-bold">{{ $goal[2] }}</div>
                                                <div class="mt-0.5 text-xs font-normal opacity-80">{{ $goal[3] }}</div>
                                            </div>
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                            <div>
                                <label class="label">Preferred campaign types (pick any)</label>
                                <div class="flex flex-wrap gap-2">
                                    @foreach(['Barter / seeding','Paid UGC','Product review','Store visit','Brand awareness','Videoshoot'] as $type)
                                        <label>
                                            <input type="checkbox" name="preferred_types[]" value="{{ $type }}" class="sr-only">
                                            <span class="pick-chip">{{ $type }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                            <div>
                                <label class="label">Monthly campaign budget</label>
                                <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
                                    @foreach(['<₹1L','₹1L–₹5L','₹5L–₹20L','₹20L+'] as $band)
                                        <label class="cursor-pointer">
                                            <input type="radio" name="budget_band" value="{{ $band }}" class="sr-only" {{ $loop->index === 1 ? 'checked' : '' }}>
                                            <div class="pick-tile">{{ $band }}</div>
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                            <div class="rounded-2xl bg-gradient-to-r from-violet-600 to-pink-600 p-5 text-white">
                                <div class="text-sm font-bold">You're set!</div>
                                <p class="mt-1 text-xs text-white/85">We'll draft your first AI campaign the moment analysis finishes.</p>
                            </div>

                            <div class="flex flex-wrap items-center justify-between gap-3 border-t border-slate-200 pt-6">
                                <button type="button" data-prev class="btn-ghost">← Back</button>
                                <button type="submit" class="btn-gradient">Enter dashboard →</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
