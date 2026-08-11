<?php

namespace App\Http\Controllers\Brand;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductImage;
use App\Support\TenantContext;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
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
            'niche' => ['nullable', 'string', 'max:190'],
            'vendor' => ['nullable', 'string', 'max:190'],
            'price_cents' => ['required', 'integer', 'min:0'],
            'inventory_qty' => ['required', 'integer', 'min:0'],
            'sku' => ['nullable', 'string', 'max:190'],
            'images'   => ['nullable', 'array', 'max:6'],
            'images.*' => ['image', 'mimes:jpg,jpeg,png,webp,gif', 'max:5120'],
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
            'niche' => $data['niche'] ?? ($workspace->settings['ai_niche'] ?? null),
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

        if ($request->hasFile('images')) {
            foreach ($request->file('images', []) as $i => $file) {
                if (! $file) continue;
                $path = $file->store("products/{$product->id}", 'public');
                ProductImage::create([
                    'product_id' => $product->id,
                    'disk' => 'public',
                    'path' => Storage::disk('public')->url($path),
                    'position' => $i,
                    'is_primary' => $i === 0,
                    'alt' => $product->title,
                ]);
            }
        }

        return redirect()->route('brand.products.index')->with('status', "{$product->title} added.");
    }

    public function show(Product $product, TenantContext $tenant)
    {
        abort_unless($product->workspace_id === $tenant->id(), 403);

        $product->load(['variants', 'images', 'collections', 'channel']);

        return view('brand.products.show', compact('product'));
    }
}
