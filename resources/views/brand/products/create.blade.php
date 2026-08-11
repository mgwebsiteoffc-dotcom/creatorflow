<x-layouts.app panel="brand" title="Add product">
    <div class="mx-auto max-w-3xl">
        <a href="{{ route('brand.products.index') }}" class="text-sm text-slate-500 hover:text-slate-800">← Products</a>
        <h1 class="mt-2 text-3xl font-black tracking-tight text-slate-900">Add product manually</h1>
        <p class="mt-1 text-sm text-slate-500">You can attach up to 6 images. First image becomes the primary.</p>

        <form method="POST" action="{{ route('brand.products.store') }}" enctype="multipart/form-data" class="card mt-6 space-y-6 p-6 md:p-8">
            @csrf

            {{-- Images --}}
            <div>
                <label class="label">Images</label>
                <div id="img-drop" class="relative cursor-pointer rounded-2xl border-2 border-dashed border-slate-300 bg-slate-50/60 p-8 text-center transition hover:border-violet-400 hover:bg-violet-50/40">
                    <input id="img-input" type="file" name="images[]" accept="image/*" multiple class="absolute inset-0 h-full w-full cursor-pointer opacity-0">
                    <div class="pointer-events-none">
                        <div class="mx-auto grid h-12 w-12 place-items-center rounded-full bg-gradient-to-br from-violet-500 to-pink-500 text-white shadow-sm">📷</div>
                        <div class="mt-3 text-sm font-semibold text-slate-800">Drop images here or click to browse</div>
                        <div class="mt-1 text-xs text-slate-500">PNG, JPG, WEBP · up to 5MB each · max 6 images</div>
                    </div>
                </div>
                <div id="img-previews" class="mt-4 hidden grid-cols-3 gap-3 sm:grid-cols-6"></div>
            </div>

            {{-- Basics --}}
            <div class="space-y-4 border-t border-slate-200 pt-6">
                <div>
                    <label class="label">Title <span class="text-rose-500">*</span></label>
                    <input class="input" name="title" value="{{ old('title') }}" required placeholder="Glow Serum 30ml">
                </div>
                <div>
                    <label class="label">Description</label>
                    <textarea class="input min-h-28" name="description" placeholder="Who is it for, what makes it special?">{{ old('description') }}</textarea>
                </div>
            </div>

            {{-- Category / niche --}}
            <div class="grid gap-4 border-t border-slate-200 pt-6 sm:grid-cols-2">
                <div>
                    <label class="label">Category</label>
                    <input class="input" name="product_type" value="{{ old('product_type') }}" placeholder="e.g. Skincare">
                </div>
                <div>
                    <label class="label">Niche</label>
                    <select class="input" name="niche">
                        <option value="">— Choose a niche —</option>
                        @foreach(['Beauty & Skincare','Fashion','Food & Beverage','Fitness','Travel','Tech','Home','Gaming','Parenting','Pets','Finance','Lifestyle','Beauty','Wellness','Toys','Automotive'] as $n)
                            <option value="{{ $n }}" @selected(old('niche') === $n)>{{ $n }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Inventory / pricing --}}
            <div class="grid gap-4 border-t border-slate-200 pt-6 sm:grid-cols-3">
                <div>
                    <label class="label">SKU</label>
                    <input class="input" name="sku" value="{{ old('sku') }}" placeholder="AUTO">
                </div>
                <div>
                    <label class="label">Price (in cents) <span class="text-rose-500">*</span></label>
                    <input class="input" type="number" name="price_cents" min="0" required value="{{ old('price_cents') }}" placeholder="2999 = $29.99">
                </div>
                <div>
                    <label class="label">Inventory qty <span class="text-rose-500">*</span></label>
                    <input class="input" type="number" name="inventory_qty" min="0" required value="{{ old('inventory_qty') }}" placeholder="100">
                </div>
            </div>

            <div class="flex flex-wrap items-center justify-between gap-3 border-t border-slate-200 pt-6">
                <a href="{{ route('brand.products.index') }}" class="btn-ghost">Cancel</a>
                <button type="submit" class="btn-primary">Add product</button>
            </div>
        </form>
    </div>

    <script>
        (function () {
            const input = document.getElementById('img-input');
            const drop = document.getElementById('img-drop');
            const previews = document.getElementById('img-previews');
            if (!input) return;

            const MAX = 6;
            const render = () => {
                const files = Array.from(input.files || []).slice(0, MAX);
                previews.innerHTML = '';
                if (!files.length) { previews.classList.add('hidden'); previews.classList.remove('grid'); return; }
                previews.classList.remove('hidden'); previews.classList.add('grid');
                files.forEach((f, i) => {
                    const url = URL.createObjectURL(f);
                    const tile = document.createElement('div');
                    tile.className = 'relative aspect-square overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm';
                    tile.innerHTML = `
                        <img src="${url}" class="h-full w-full object-cover" alt="">
                        ${i === 0 ? '<span class="absolute left-1.5 top-1.5 rounded-full bg-violet-600 px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider text-white">Primary</span>' : ''}
                        <span class="absolute inset-x-0 bottom-0 truncate bg-black/50 px-2 py-1 text-[10px] text-white">${f.name}</span>
                    `;
                    previews.appendChild(tile);
                });
            };
            input.addEventListener('change', render);

            ['dragover','dragenter'].forEach(evt => drop.addEventListener(evt, e => {
                e.preventDefault(); drop.classList.add('border-violet-500','bg-violet-50');
            }));
            ['dragleave','drop'].forEach(evt => drop.addEventListener(evt, e => {
                e.preventDefault(); drop.classList.remove('border-violet-500','bg-violet-50');
            }));
            drop.addEventListener('drop', (e) => {
                if (e.dataTransfer?.files?.length) { input.files = e.dataTransfer.files; render(); }
            });
        })();
    </script>
</x-layouts.app>
