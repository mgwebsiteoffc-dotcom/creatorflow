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
            'price' => ['required', 'numeric', 'min:0'],
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
            'price_cents' => (int) round(((float) $data['price']) * 100),
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

    public function edit(Product $product, TenantContext $tenant)
    {
        abort_unless($product->workspace_id === $tenant->id(), 403);
        $product->load(['variants', 'images']);
        return view('brand.products.edit', compact('product'));
    }

    public function update(Product $product, Request $request, TenantContext $tenant)
    {
        abort_unless($product->workspace_id === $tenant->id(), 403);

        $data = $request->validate([
            'title'         => ['required', 'string', 'max:255'],
            'description'   => ['nullable', 'string'],
            'product_type'  => ['nullable', 'string', 'max:190'],
            'niche'         => ['nullable', 'string', 'max:190'],
            'vendor'        => ['nullable', 'string', 'max:190'],
            'status'        => ['required', 'in:active,draft,archived'],
            'price'         => ['required', 'numeric', 'min:0'],
            'inventory_qty' => ['required', 'integer', 'min:0'],
            'sku'           => ['nullable', 'string', 'max:190'],
            'images'        => ['nullable', 'array', 'max:6'],
            'images.*'      => ['image', 'mimes:jpg,jpeg,png,webp,gif', 'max:5120'],
            'remove_images' => ['nullable', 'array'],
            'remove_images.*' => ['integer'],
        ]);

        $product->update([
            'title'         => $data['title'],
            'description'   => $data['description'] ?? null,
            'product_type'  => $data['product_type'] ?? null,
            'vendor'        => $data['vendor'] ?? $product->vendor,
            'niche'         => $data['niche'] ?? $product->niche,
            'status'        => $data['status'],
        ]);

        // Keep the primary "Default" variant in sync — this is the single-variant
        // MVP flow; multi-variant editing gets its own screen later.
        $variant = $product->variants()->first();
        if ($variant) {
            $variant->update([
                'sku'           => $data['sku'] ?? $variant->sku,
                'price_cents'   => (int) round(((float) $data['price']) * 100),
                'inventory_qty' => $data['inventory_qty'],
            ]);
        }

        // Remove any images the user un-checked in the edit form.
        if (! empty($data['remove_images'])) {
            $ids = collect($data['remove_images'])->map('intval')->all();
            $toDrop = $product->images()->whereIn('id', $ids)->get();
            foreach ($toDrop as $img) {
                // Best-effort disk cleanup.
                if ($img->disk === 'public' && $img->path && Storage::disk('public')->exists(basename($img->path))) {
                    try { Storage::disk('public')->delete(basename($img->path)); } catch (\Throwable $e) {}
                }
                $img->delete();
            }
        }

        // Append any new uploads at the end of the gallery.
        if ($request->hasFile('images')) {
            $nextPos = (int) $product->images()->max('position') + 1;
            foreach ($request->file('images', []) as $file) {
                if (! $file) continue;
                $path = $file->store("products/{$product->id}", 'public');
                ProductImage::create([
                    'product_id' => $product->id,
                    'disk'       => 'public',
                    'path'       => Storage::disk('public')->url($path),
                    'position'   => $nextPos++,
                    'is_primary' => $product->images()->count() === 0,
                    'alt'        => $product->title,
                ]);
            }
        }

        return redirect()->route('brand.products.show', $product)
            ->with('status', "{$product->title} updated.");
    }

    public function destroy(Product $product, TenantContext $tenant)
    {
        abort_unless($product->workspace_id === $tenant->id(), 403);

        // Guard: don't allow deleting a product that is referenced by an active campaign.
        $activeUse = \App\Models\CampaignProduct::where('product_id', $product->id)
            ->whereHas('campaign', fn ($q) => $q->whereIn('status', ['live', 'draft', 'paused']))
            ->exists();

        if ($activeUse) {
            return back()->with('error', "This product is used in an active or draft campaign — end those campaigns first.");
        }

        $title = $product->title;
        $product->delete();

        return redirect()->route('brand.products.index')
            ->with('status', "{$title} deleted.");
    }
}
