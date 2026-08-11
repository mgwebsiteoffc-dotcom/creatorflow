<x-layouts.app panel="brand" :title="$product->title">
    <a href="{{ route('brand.products.index') }}" class="text-sm text-slate-500">← Products</a>
    <div class="mt-2 grid gap-5 md:grid-cols-3">
        <div class="card md:col-span-2 overflow-hidden">
            <div class="aspect-video bg-slate-100">
                @if($product->images->first())
                    <img src="{{ $product->images->first()->path }}" class="h-full w-full object-cover">
                @else
                    <div class="grid h-full place-items-center text-5xl">📦</div>
                @endif
            </div>
            <div class="p-5">
                <h1 class="text-2xl font-bold">{{ $product->title }}</h1>
                <p class="mt-1 text-slate-600">{{ $product->description }}</p>
                <div class="mt-4 flex flex-wrap gap-2">
                    <x-badge tone="violet">{{ $product->niche ?: 'No niche yet' }}</x-badge>
                    @if($product->hero_score > 70)<x-badge tone="amber">Hero {{ $product->hero_score }}</x-badge>@endif
                    <x-badge tone="slate">{{ $product->product_type ?: 'Uncategorized' }}</x-badge>
                    @if($product->channel)<x-badge tone="sky">{{ $product->channel->type }}</x-badge>@endif
                </div>
            </div>
        </div>

        <div class="card space-y-4 p-5">
            <div>
                <p class="text-xs text-slate-500">Price from</p>
                <p class="text-2xl font-bold">{{ $currentWorkspace->formatMoney((int) $product->priceCents()) }}</p>
            </div>
            <div>
                <p class="text-xs text-slate-500">Total inventory</p>
                <p class="text-2xl font-bold">{{ $product->inventoryTotal() }}</p>
            </div>
            <div>
                <p class="text-xs font-medium text-slate-600">Variants</p>
                <ul class="mt-1 divide-y divide-slate-100 text-sm">
                    @foreach($product->variants as $v)
                        <li class="flex justify-between py-1.5">
                            <span>{{ $v->title ?: $v->sku ?: 'Default' }}</span>
                            <span class="text-slate-500">{{ $currentWorkspace->formatMoney((int) $v->price_cents) }} · {{ $v->inventory_qty }} in stock</span>
                        </li>
                    @endforeach
                </ul>
            </div>
            @if($product->ai_analysis)
                <div class="rounded-xl bg-violet-50 p-3 text-sm text-violet-800">
                    <p class="font-semibold">✨ AI analysis</p>
                    <p>Suitability: {{ $product->ai_analysis['suitability'] ?? '—' }}</p>
                </div>
            @endif
        </div>
    </div>
</x-layouts.app>
