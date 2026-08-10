<x-layouts.app panel="brand" title="Get started">
    <h1 class="text-2xl font-bold">Let's get you set up</h1>
    <p class="mt-1 text-sm text-slate-500">Connect a store or add products. AI then detects your niche and hero products and drafts a campaign.</p>

    <div class="mt-6 grid gap-4 md:grid-cols-3">
        <div class="card p-5">
            <div class="text-2xl">🛍️</div>
            <h3 class="mt-2 font-semibold">1. Connect Shopify</h3>
            <p class="mt-1 text-sm text-slate-500">Auto-sync products, inventory, images and orders in real time.</p>
            <form method="POST" action="{{ route('brand.onboarding.shopify') }}" class="mt-4">
                @csrf
                <button class="btn-primary w-full" {{ $shopifyConnected ? 'disabled' : '' }}>
                    {{ $shopifyConnected ? '✓ Connected' : 'Install Shopify app' }}
                </button>
            </form>
        </div>

        <div class="card p-5">
            <div class="text-2xl">📄</div>
            <h3 class="mt-2 font-semibold">Upload CSV</h3>
            <p class="mt-1 text-sm text-slate-500">Bring products from WooCommerce, Amazon or a spreadsheet.</p>
            <a href="{{ route('brand.products.import') }}" class="btn-secondary mt-4 w-full">Import products</a>
        </div>

        <div class="card p-5">
            <div class="text-2xl">➕</div>
            <h3 class="mt-2 font-semibold">Add manually</h3>
            <form method="POST" action="{{ route('brand.onboarding.product') }}" class="mt-4 space-y-2">
                @csrf
                <input class="input" name="title" placeholder="Product title" required>
                <div class="grid grid-cols-2 gap-2">
                    <input class="input" name="price_cents" type="number" min="0" placeholder="Price in cents" required>
                    <input class="input" name="inventory_qty" type="number" min="0" placeholder="Stock" required>
                </div>
                <button class="btn-secondary w-full">Add product</button>
            </form>
        </div>
    </div>

    @if($productsCount > 0)
        <div class="card mt-6 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="font-semibold">2. Run AI analysis</h3>
                    <p class="text-sm text-slate-500">{{ $productsCount }} products ready. Detect niche, hero products and content angles.</p>
                </div>
                <form method="POST" action="{{ route('brand.onboarding.analyze') }}">
                    @csrf
                    <button class="btn-primary">✨ Analyze catalog</button>
                </form>
            </div>
        </div>

        <form method="POST" action="{{ route('brand.onboarding.complete') }}">
            @csrf
            <button class="btn-primary mt-4 w-full md:w-auto">Continue to dashboard →</button>
        </form>
    @endif
</x-layouts.app>
