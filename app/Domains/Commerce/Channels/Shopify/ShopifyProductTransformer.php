<?php

namespace App\Domains\Commerce\Channels\Shopify;

use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;

/**
 * Maps Shopify product payloads into CreatorFlow's channel-agnostic
 * product/variant/image shape.
 */
class ShopifyProductTransformer
{
    /**
     * @return array{product: array<string,mixed>, variants: array<int,array<string,mixed>>, images: array<int,array<string,mixed>>}
     */
    public function transform(array $payload): array
    {
        $primaryVariantId = $payload['image'] ? null : null;

        $variants = collect($payload['variants'] ?? [])->map(fn (array $v) => [
            'external_id' => (string) ($v['id'] ?? ''),
            'sku' => $v['sku'] ?? null,
            'title' => $v['title'] ?? null,
            'price_cents' => $this->toCents($v['price'] ?? 0),
            'compare_at_cents' => isset($v['compare_at_price']) ? $this->toCents($v['compare_at_price']) : null,
            'currency' => $payload['currency'] ?? 'USD',
            'inventory_qty' => (int) ($v['inventory_quantity'] ?? 0),
            'inventory_policy' => $v['inventory_policy'] ?? 'continue',
            'barcode' => $v['barcode'] ?? null,
            'weight' => isset($v['grams']) ? round(((int) $v['grams']) / 1000, 3) : null,
            'requires_shipping' => (bool) ($v['requires_shipping'] ?? true),
        ])->toArray();

        $images = collect($payload['images'] ?? [])->map(fn (array $img, int $i) => [
            'external_id' => (string) ($img['id'] ?? ''),
            'disk' => 's3',
            'path' => $img['src'] ?? '',
            'alt' => $img['alt'] ?? $payload['title'] ?? null,
            'position' => (int) ($img['position'] ?? $i + 1),
            'is_primary' => (int) ($img['position'] ?? ($i + 1)) === 1,
            'width' => $img['width'] ?? null,
            'height' => $img['height'] ?? null,
        ])->toArray();

        $product = [
            'external_id' => (string) ($payload['id'] ?? ''),
            'title' => $payload['title'] ?? 'Untitled',
            'description' => strip_tags((string) ($payload['body_html'] ?? '')),
            'vendor' => $payload['vendor'] ?? null,
            'product_type' => $payload['product_type'] ?? null,
            'tags' => $this->parseTags($payload['tags'] ?? ''),
            'status' => strtolower((string) ($payload['status'] ?? 'active')),
            'metadata' => [
                'handle' => $payload['handle'] ?? null,
                'published_at' => $payload['published_at'] ?? null,
                'shopify' => [
                    'product_type' => $payload['product_type'] ?? null,
                    'vendor' => $payload['vendor'] ?? null,
                ],
            ],
        ];

        return compact('product', 'variants', 'images');
    }

    /**
     * Persist a transformed Shopify payload, updating if it already exists.
     */
    public function upsert(int $workspaceId, int $channelId, array $payload): Product
    {
        $data = $this->transform($payload);

        $product = Product::updateOrCreate(
            ['workspace_id' => $workspaceId, 'external_id' => $data['product']['external_id']],
            array_merge($data['product'], ['workspace_id' => $workspaceId, 'channel_id' => $channelId])
        );

        $this->syncVariants($product, $data['variants']);
        $this->syncImages($product, $data['images']);

        return $product;
    }

    protected function syncVariants(Product $product, array $variants): void
    {
        $externalIds = array_filter(array_column($variants, 'external_id'));

        $product->variants()
            ->when($externalIds, fn ($q) => $q->whereNotIn('external_id', $externalIds))
            ->delete();

        foreach ($variants as $variant) {
            $product->variants()->updateOrCreate(
                ['external_id' => $variant['external_id']],
                $variant
            );
        }
    }

    protected function syncImages(Product $product, array $images): void
    {
        // For MVP we replace the image set; production would diff by external_id.
        $product->images()->delete();

        foreach ($images as $image) {
            ProductImage::create(array_merge($image, ['product_id' => $product->id]));
        }
    }

    protected function toCents(mixed $value): int
    {
        return (int) round(((float) $value) * 100);
    }

    /**
     * @return array<int, string>
     */
    protected function parseTags(mixed $tags): array
    {
        if (is_array($tags)) {
            return $tags;
        }

        return array_values(array_filter(array_map('trim', explode(',', (string) $tags))));
    }
}
