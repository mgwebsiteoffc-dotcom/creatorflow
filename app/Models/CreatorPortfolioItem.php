<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CreatorPortfolioItem extends Model
{
    protected $fillable = [
        'creator_id', 'type', 'title', 'disk', 'path', 'external_url',
        'thumbnail_path', 'description', 'metrics', 'position',
    ];

    protected $casts = ['metrics' => 'array'];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(Creator::class);
    }
}
