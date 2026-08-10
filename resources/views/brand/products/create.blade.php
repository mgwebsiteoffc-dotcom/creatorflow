<x-layouts.app panel="brand" title="Add product">
    <a href="{{ route('brand.products.index') }}" class="text-sm text-slate-500">← Products</a>
    <h1 class="mt-1 text-2xl font-bold">Add product manually</h1>

    <form method="POST" action="{{ route('brand.products.store') }}" class="card mt-5 max-w-xl space-y-4 p-5">
        @csrf
        <div>
            <label class="label">Title</label>
            <input class="input" name="title" required>
        </div>
        <div>
            <label class="label">Description</label>
            <textarea class="input min-h-28" name="description"></textarea>
        </div>
        <div class="grid grid-cols-2 gap-3">
            <div>
                <label class="label">Type / category</label>
                <input class="input" name="product_type" placeholder="e.g. Skincare">
            </div>
            <div>
                <label class="label">SKU</label>
                <input class="input" name="sku">
            </div>
        </div>
        <div class="grid grid-cols-2 gap-3">
            <div>
                <label class="label">Price (in cents)</label>
                <input class="input" type="number" name="price_cents" min="0" placeholder="2999 = $29.99" required>
            </div>
            <div>
                <label class="label">Inventory quantity</label>
                <input class="input" type="number" name="inventory_qty" min="0" required>
            </div>
        </div>
        <button class="btn-primary w-full">Add product</button>
    </form>
</x-layouts.app>
