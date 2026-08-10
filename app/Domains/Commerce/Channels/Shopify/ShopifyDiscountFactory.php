<?php

namespace App\Domains\Commerce\Channels\Shopify;

use App\Models\Creator;
use App\Models\Product;
use Illuminate\Support\Str;

class ShopifyDiscountFactory
{
    /**
     * Generate a unique, human-readable code for a creator.
     */
    public function makeCode(Creator $creator, ?string $prefix = null): string
    {
        $base = Str::upper(Str::slug($creator->display_name, ''));
        $base = preg_replace('/[^A-Z0-9]/', '', $base) ?: 'CREATOR';
        $base = substr($base, 0, 12);
        $suffix = Str::upper(Str::random(4));

        return ($prefix ? Str::upper($prefix).'-' : '')."{$base}-{$suffix}";
    }

    /**
     * Build the Shopify price rule payload for a code.
     *
     * @param  array<string, mixed>  $options
     * @return array<string, mixed>
     */
    public function priceRulePayload(string $code, Product $product, Creator $creator, array $options): array
    {
        $type = $options['type'] ?? 'full_comp';

        $value = match ($type) {
            'percentage' => '-'.((float) ($options['value'] ?? 10)).'.0',
            'fixed_amount' => '-'.((float) ($options['value'] ?? 10)).'.0',
            'full_comp' => '-100.0',
            default => '-100.0',
        };

        $valueType = $type === 'fixed_amount' ? 'fixed_amount' : 'percentage';
        $targetType = $type === 'free_shipping' ? 'shipping_line' : 'line_item';

        return [
            'price_rule' => [
                'title' => "CreatorFlow: {$code}",
                'target_type' => $targetType,
                'target_selection' => 'all',
                'allocation_method' => $type === 'fixed_amount' ? 'across' : 'each',
                'value_type' => $valueType,
                'value' => $value,
                'customer_selection' => 'all',
                'once_per_customer' => true,
                'usage_limit' => (int) ($options['usage_limit'] ?? 1),
                'starts_at' => now()->toIso8601String(),
                'ends_at' => isset($options['expires_at'])
                    ? \Carbon\Carbon::parse($options['expires_at'])->toIso8601String()
                    : now()->addMonths(3)->toIso8601String(),
                'entitled_product_ids' => array_filter([$product->external_id]),
            ],
        ];
    }
}
