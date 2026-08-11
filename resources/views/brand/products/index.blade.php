<x-layouts.app panel="brand" title="Products">
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <h1 class="text-3xl font-black tracking-tight text-slate-900">Products</h1>
            <p class="mt-1 text-sm text-slate-500">Synced, uploaded or manually added — all in one catalog.</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('brand.products.import') }}" class="btn-secondary !py-2 text-sm">Import CSV</a>
            <a href="{{ route('brand.products.create') }}" class="btn-primary !py-2 text-sm">+ Add product</a>
        </div>
    </div>

    <form method="GET" class="mt-6 flex gap-2">
        <input class="input" name="q" value="{{ request('q') }}" placeholder="Search products…">
        <button class="btn-secondary">Search</button>
    </form>

    <div class="mt-6 grid grid-cols-2 gap-4 md:grid-cols-3 lg:grid-cols-4">
        @forelse($products as $product)
            <a href="{{ route('brand.products.show', $product) }}" class="card overflow-hidden hover:border-violet-300">
                <div class="aspect-square bg-slate-100">
                    @if($product->images->first())
                        <img src="{{ $product->images->first()->path }}" class="h-full w-full object-cover" alt="">
                    @else
                        <div class="grid h-full place-items-center text-3xl">📦</div>
                    @endif
                </div>
                <div class="p-3">
                    <p class="truncate text-sm font-semibold">{{ $product->title }}</p>
                    <div class="mt-1 flex items-center justify-between text-xs text-slate-500">
                        <span>${{ number_format($product->priceCents()/100, 2) }}</span>
                        <span>{{ $product->inventoryTotal() }} in stock</span>
                    </div>
                    @if($product->hero_score > 70)
                        <span class="badge-amber mt-2">Hero {{ $product->hero_score }}</span>
                    @endif
                    @if($product->channel)
                        <span class="badge-slate mt-2 capitalize">{{ $product->channel->type }}</span>
                    @endif
                </div>
            </a>
        @empty
            <x-empty-state title="No products" icon="📦" class="col-span-full">
                Connect Shopify, import a CSV or add a product manually.
                <x-slot:action><a href="{{ route('brand.onboarding') }}" class="btn-primary">Add products</a></x-slot:action>
            </x-empty-state>
        @endforelse
    </div>

    <div class="mt-8">{{ $products->links() }}</div>
</x-layouts.app>
