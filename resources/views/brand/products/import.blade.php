<x-layouts.app panel="brand" title="Import products">
    <a href="{{ route('brand.products.index') }}" class="text-sm text-slate-500">← Products</a>
    <h1 class="mt-1 text-2xl font-bold">Import products from CSV</h1>
    <p class="text-sm text-slate-500">Works with exports from Shopify, WooCommerce, Amazon or any spreadsheet.</p>

    <div class="mt-5 grid gap-5 md:grid-cols-3">
        <form method="POST" action="{{ route('brand.products.import.store') }}" enctype="multipart/form-data" class="card space-y-4 p-5 md:col-span-2">
            @csrf
            <div>
                <label class="label">CSV file</label>
                <input type="file" name="csv" accept=".csv,text/csv" class="block w-full text-sm" required>
                <p class="mt-1 text-xs text-slate-500">Max 5 MB. We analyze products after import and assign hero scores.</p>
            </div>
            <button class="btn-primary">Start import</button>
        </form>

        <div class="card p-5">
            <h3 class="font-semibold">Expected columns</h3>
            <ul class="mt-2 space-y-1 text-sm text-slate-600">
                @foreach($sampleHeaders as $h)
                    <li><code class="rounded bg-slate-100 px-1.5 py-0.5 text-xs">{{ $h }}</code></li>
                @endforeach
            </ul>
            <p class="mt-3 text-xs text-slate-500">Only <code>title</code> and <code>price</code> are required.</p>
        </div>
    </div>
</x-layouts.app>
