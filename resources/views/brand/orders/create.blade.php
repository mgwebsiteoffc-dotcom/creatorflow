<x-layouts.app panel="brand" title="Create manual order">
    <a href="{{ route('brand.orders.index') }}" class="text-sm text-slate-500">← Orders</a>
    <div class="mt-1 flex items-end justify-between gap-4">
        <div>
            <p class="text-xs font-bold uppercase tracking-widest text-violet-600">🎁 Gifting / seeding</p>
            <h1 class="mt-1 text-3xl font-black tracking-tight text-slate-900">Create manual order</h1>
            <p class="mt-1 text-sm text-slate-500">Ship a product to a creator without going through Shopify — record it here and add tracking when you dispatch.</p>
        </div>
    </div>

    @if($errors->any())
        <div class="mt-4 rounded-xl border border-rose-200 bg-rose-50 p-3 text-xs text-rose-700">
            @foreach($errors->all() as $err)<p>• {{ $err }}</p>@endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('brand.orders.store') }}" class="mt-6 grid gap-6 lg:grid-cols-3">
        @csrf

        {{-- Left: creator + product --}}
        <div class="lg:col-span-2 space-y-6">
            <section class="rounded-2xl border border-slate-200 bg-white p-6">
                <h2 class="text-lg font-black text-slate-900">1. Who's it for</h2>

                <div class="mt-4">
                    <label class="label">Creator <span class="text-rose-500">*</span></label>
                    <select class="input" name="creator_id" required>
                        <option value="">— Pick a creator —</option>
                        @foreach($creators as $c)
                            <option value="{{ $c->id }}" @selected(old('creator_id') == $c->id)>
                                {{ $c->display_name }}{{ $c->city ? ' · '.$c->city : '' }}
                            </option>
                        @endforeach
                    </select>
                    <p class="mt-1 text-xs text-slate-500">Not seeing them? Send them an invite through a campaign first — they need to be on the platform.</p>
                </div>
            </section>

            <section class="rounded-2xl border border-slate-200 bg-white p-6">
                <h2 class="text-lg font-black text-slate-900">2. What are you shipping</h2>

                @if($products->isEmpty())
                    <div class="mt-4 rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-900">
                        You don't have any active products yet.
                        <a href="{{ route('brand.products.create') }}" class="font-bold underline">Add a product →</a>
                    </div>
                @else
                    <div class="mt-4 grid gap-3 sm:grid-cols-2">
                        <div>
                            <label class="label">Product <span class="text-rose-500">*</span></label>
                            <select class="input" name="product_id" id="product-select" required>
                                <option value="">— Pick a product —</option>
                                @foreach($products as $p)
                                    <option value="{{ $p->id }}" @selected(old('product_id') == $p->id)>{{ $p->title }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="label">Variant <span class="text-slate-400">(optional)</span></label>
                            <select class="input" name="variant_id" id="variant-select">
                                <option value="">— Default —</option>
                            </select>
                        </div>
                        <div>
                            <label class="label">Quantity</label>
                            <input class="input" type="number" name="quantity" min="1" max="99" value="{{ old('quantity', 1) }}" required>
                        </div>
                    </div>
                    <div class="mt-4">
                        <label class="label">Note to yourself <span class="text-slate-400">(optional)</span></label>
                        <textarea class="input min-h-20" name="note" placeholder="e.g. Include free samples · handwritten thank-you note">{{ old('note') }}</textarea>
                    </div>
                @endif
            </section>
        </div>

        {{-- Right: shipping address --}}
        <aside class="space-y-6">
            <section class="rounded-2xl border border-slate-200 bg-white p-6">
                <h2 class="text-lg font-black text-slate-900">3. Ship to</h2>
                <div class="mt-4 space-y-3">
                    <div>
                        <label class="label">Address line 1 <span class="text-rose-500">*</span></label>
                        <input class="input" name="ship_address_line1" value="{{ old('ship_address_line1') }}" required placeholder="Flat / house no · street">
                    </div>
                    <div>
                        <label class="label">Address line 2 <span class="text-slate-400">(optional)</span></label>
                        <input class="input" name="ship_address_line2" value="{{ old('ship_address_line2') }}" placeholder="Landmark · area">
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="label">City <span class="text-rose-500">*</span></label>
                            <input class="input" name="ship_city" value="{{ old('ship_city') }}" required>
                        </div>
                        <div>
                            <label class="label">State</label>
                            <input class="input" name="ship_state" value="{{ old('ship_state') }}">
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="label">PIN code <span class="text-rose-500">*</span></label>
                            <input class="input" name="ship_postal" value="{{ old('ship_postal') }}" required inputmode="numeric" pattern="[0-9]{6}" placeholder="6 digits">
                        </div>
                        <div>
                            <label class="label">Country</label>
                            <input class="input" name="ship_country" value="{{ old('ship_country', 'IN') }}" maxlength="80">
                        </div>
                    </div>
                    <div>
                        <label class="label">Contact phone</label>
                        <input class="input" name="ship_phone" value="{{ old('ship_phone') }}" placeholder="10-digit mobile">
                    </div>
                </div>
            </section>

            <button class="btn-primary w-full" @if($products->isEmpty()) disabled title="Add a product first" @endif>
                Create order
            </button>
            <a href="{{ route('brand.orders.index') }}" class="block text-center text-xs text-slate-500 hover:text-slate-800">Cancel</a>
        </aside>
    </form>

    {{-- Populate the variant dropdown as the product changes --}}
    <script>
        (function () {
            const productData = @json($products->mapWithKeys(fn($p) => [$p->id => $p->variants->map(fn($v) => [
                'id' => $v->id,
                'title' => $v->title ?: 'Default',
                'sku' => $v->sku,
                'price' => $v->price_cents,
                'stock' => $v->inventory_qty,
            ])->values()->all()])->all() ?: '{}');

            const productSel = document.getElementById('product-select');
            const variantSel = document.getElementById('variant-select');
            if (! productSel || ! variantSel) return;

            function rebuild() {
                const id = productSel.value;
                const variants = productData[id] || [];
                variantSel.innerHTML = '<option value="">— Default —</option>';
                variants.forEach(v => {
                    const opt = document.createElement('option');
                    opt.value = v.id;
                    const price = (v.price / 100).toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                    opt.textContent = `${v.title}${v.sku ? ' · '+v.sku : ''} · ₹${price}${v.stock !== null ? ' · '+v.stock+' in stock' : ''}`;
                    variantSel.appendChild(opt);
                });
            }
            productSel.addEventListener('change', rebuild);
            rebuild();
        })();
    </script>
</x-layouts.app>
