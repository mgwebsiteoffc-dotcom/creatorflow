<?php

namespace App\Models;

use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasUuid;

    protected $fillable = [
        'uuid', 'workspace_id', 'channel_id', 'external_id', 'title',
        'description', 'vendor', 'product_type', 'niche', 'hero_score',
        'status', 'tags', 'metadata', 'ai_analysis',
    ];

    protected $casts = [
        'tags' => 'array',
        'metadata' => 'array',
        'ai_analysis' => 'array',
        'hero_score' => 'decimal:2',
    ];

    public function workspace(): BelongsTo
    {
        return $this->belongsTo(Workspace::class);
    }

    public function channel(): BelongsTo
    {
        return $this->belongsTo(Channel::class);
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)->orderBy('position');
    }

    public function primaryImage(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(ProductImage::class)->where('is_primary', true)->ofMany('position', 'min');
    }

    public function collections(): BelongsToMany
    {
        return $this->belongsToMany(Collection::class)->withPivot('position');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function priceCents(): int
    {
        return (int) ($this->variants()->min('price_cents') ?? 0);
    }

    public function inventoryTotal(): int
    {
        return (int) $this->variants()->sum('inventory_qty');
    }
}
