<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CreatorPreference extends Model
{
    public $incrementing = false;

    protected $primaryKey = 'creator_id';

    protected $fillable = [
        'creator_id', 'barter_product_categories', 'min_paid_cents',
        'shipping_address', 'clothing_size', 'skin_tone', 'hair_type',
        'availability_status', 'response_time_hours', 'notifications_json',
    ];

    protected $casts = [
        'barter_product_categories' => 'array',
        'shipping_address' => 'array',
        'notifications_json' => 'array',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(Creator::class);
    }
}
