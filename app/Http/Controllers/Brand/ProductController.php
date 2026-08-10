<?php

namespace App\Http\Controllers\Brand;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Support\TenantContext;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index(Request $request, TenantContext $tenant)
    {
        $products = $tenant->active()->products()
            ->with(['variants', 'images', 'channel'])
            ->when($request->get('q'), fn ($q, $term) => $q->where('title', 'like', "%{$term}%"))
            ->orderByDesc('hero_score')
            ->paginate(24)
            ->withQueryString();

        return view('brand.products.index', compact('products'));
    }

    public function create()
    {
        return view('brand.products.create');
    }

    public function store(Request $request, TenantContext $tenant)
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'product_type' => ['nullable', 'string', 'max:190'],
            'vendor' => ['nullable', 'string', 'max:190'],
            'price_cents' => ['required', 'integer', 'min:0'],
            'inventory_qty' => ['required', 'integer', 'min:0'],
            'sku' => ['nullable', 'string', 'max:190'],
        ]);

        $workspace = $tenant->active();
        $channel = $workspace->channels()->firstOrCreate(
            ['type' => 'manual'],
            ['name' => 'Manual', 'status' => 'active']
        );

        $product = Product::create([
            'uuid' => (string) Str::uuid(),
            'workspace_id' => $workspace->id,
            'channel_id' => $channel->id,
            'external_id' => 'manual_'.Str::uuid(),
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'product_type' => $data['product_type'] ?? null,
            'vendor' => $data['vendor'] ?? $workspace->name,
            'niche' => $workspace->settings['ai_niche'] ?? null,
            'status' => 'active',
            'tags' => [],
            'metadata' => ['source' => 'manual'],
        ]);

        $product->variants()->create([
            'sku' => $data['sku'] ?? Str::upper(Str::random(8)),
            'title' => 'Default',
            'price_cents' => $data['price_cents'],
            'inventory_qty' => $data['inventory_qty'],
            'currency' => $workspace->currency,
        ]);

        return redirect()->route('brand.products.index')->with('status', "{$product->title} added.");
    }

    public function show(Product $product, TenantContext $tenant)
    {
        abort_unless($product->workspace_id === $tenant->id(), 403);

        $product->load(['variants', 'images', 'collections', 'channel']);

        return view('brand.products.show', compact('product'));
    }
}
