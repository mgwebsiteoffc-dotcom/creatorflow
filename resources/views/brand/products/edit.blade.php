<x-layouts.app panel="brand" title="Edit product">
    <a href="{{ route('brand.products.show', $product) }}" class="text-sm text-slate-500">← Back to {{ $product->title }}</a>
    <div class="mt-2 flex flex-wrap items-end justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black tracking-tight text-slate-900 md:text-3xl">Edit product</h1>
            <p class="mt-1 text-sm text-slate-500">Change details, refresh photos, or archive to hide from new campaigns.</p>
        </div>
        <form method="POST" action="{{ route('brand.products.destroy', $product) }}" data-confirm="Delete {{ $product->title }}? This can't be undone.">
            @csrf @method('DELETE')
            <button class="btn-danger">
                <x-icon name="trash" class="h-4 w-4" /> Delete product
            </button>
        </form>
    </div>

    @php
        $variant = $product->variants->first();
        $priceInr = $variant ? number_format($variant->price_cents / 100, 2, '.', '') : '';
        $inventory = $variant ? $variant->inventory_qty : 0;
        $sku       = $variant ? $variant->sku : '';
    @endphp

    <form method="POST" action="{{ route('brand.products.update', $product) }}" enctype="multipart/form-data"
          class="card mt-5 p-5 space-y-5">
        @csrf @method('PATCH')

        <div class="grid gap-4 md:grid-cols-3">
            <div class="md:col-span-2 space-y-4">
                <div>
                    <label class="label">Title <span class="text-rose-500">*</span></label>
                    <input class="input" name="title" required value="{{ old('title', $product->title) }}">
                </div>
                <div>
                    <label class="label">Description</label>
                    <textarea class="input min-h-32" name="description" placeholder="Short description brands + creators see on the shortlist card…">{{ old('description', $product->description) }}</textarea>
                </div>
                <div class="grid gap-3 sm:grid-cols-3">
                    <div>
                        <label class="label">Product type</label>
                        <input class="input" name="product_type" value="{{ old('product_type', $product->product_type) }}" placeholder="Serum, Snack, Kurta…">
                    </div>
                    <div>
                        <label class="label">Niche</label>
                        <input class="input" name="niche" value="{{ old('niche', $product->niche) }}" placeholder="Beauty · Food…">
                    </div>
                    <div>
                        <label class="label">Vendor</label>
                        <input class="input" name="vendor" value="{{ old('vendor', $product->vendor) }}">
                    </div>
                </div>
            </div>

            <div class="space-y-4">
                <div>
                    <label class="label">Status</label>
                    <select class="input" name="status">
                        @foreach(['active' => 'Active', 'draft' => 'Draft', 'archived' => 'Archived'] as $v => $l)
                            <option value="{{ $v }}" @selected(old('status', $product->status) === $v)>{{ $l }}</option>
                        @endforeach
                    </select>
                    <p class="mt-1 text-xs text-slate-500">Draft + Archived are hidden from the campaign picker.</p>
                </div>
                <div>
                    <label class="label">Price (₹) <span class="text-rose-500">*</span></label>
                    <input class="input" type="number" step="0.01" min="0" name="price" required value="{{ old('price', $priceInr) }}">
                </div>
                <div>
                    <label class="label">Inventory (units) <span class="text-rose-500">*</span></label>
                    <input class="input" type="number" min="0" name="inventory_qty" required value="{{ old('inventory_qty', $inventory) }}">
                </div>
                <div>
                    <label class="label">SKU</label>
                    <input class="input" name="sku" value="{{ old('sku', $sku) }}" placeholder="AUTO-XXXX">
                </div>
            </div>
        </div>

        {{-- Existing images with per-image "remove" checkbox --}}
        @if($product->images->isNotEmpty())
            <div>
                <label class="label">Existing photos</label>
                <div class="grid grid-cols-2 gap-3 sm:grid-cols-4 md:grid-cols-6">
                    @foreach($product->images as $img)
                        <label class="group relative block cursor-pointer overflow-hidden rounded-lg border border-slate-200 bg-white transition has-[:checked]:border-rose-400 has-[:checked]:opacity-50">
                            <img src="{{ $img->path }}" alt="" class="aspect-square w-full object-cover">
                            <input type="checkbox" name="remove_images[]" value="{{ $img->id }}" class="peer sr-only">
                            <span class="absolute inset-x-0 bottom-0 flex items-center justify-center gap-1 bg-white/90 py-1 text-[10px] font-semibold text-slate-500 peer-checked:bg-rose-500 peer-checked:text-white">
                                <x-icon name="trash" class="h-3 w-3" /> <span class="peer-checked:hidden">Remove</span><span class="hidden peer-checked:inline">Removing</span>
                            </span>
                        </label>
                    @endforeach
                </div>
                <p class="mt-1 text-[11px] text-slate-400">Tick a photo to remove it when you save.</p>
            </div>
        @endif

        <div>
            <label class="label">Add more photos (max 6)</label>
            <input type="file" name="images[]" multiple accept="image/*" class="text-xs text-slate-500 file:mr-3 file:cursor-pointer file:rounded-md file:border-0 file:bg-slate-100 file:px-3 file:py-1.5 file:text-xs file:font-semibold file:text-slate-700 hover:file:bg-slate-200">
        </div>

        <div class="flex flex-wrap items-center justify-between gap-2 border-t border-slate-100 pt-4">
            <a href="{{ route('brand.products.show', $product) }}" class="btn-ghost">Cancel</a>
            <button class="btn-primary">Save changes</button>
        </div>
    </form>
</x-layouts.app>
