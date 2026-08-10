<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductVariant extends Model
{
    protected $fillable = [
        'product_id', 'external_id', 'sku', 'title', 'price_cents',
        'compare_at_cents', 'currency', 'inventory_qty', 'inventory_policy',
        'barcode', 'weight', 'requires_shipping',
    ];

    protected $casts = [
        'price_cents' => 'integer',
        'compare_at_cents' => 'integer',
        'inventory_qty' => 'integer',
        'requires_shipping' => 'boolean',
        'weight' => 'decimal:2',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
