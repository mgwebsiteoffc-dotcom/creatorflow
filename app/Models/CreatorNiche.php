<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CreatorNiche extends Model
{
    public $timestamps = false;

    protected $fillable = ['creator_id', 'niche'];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(Creator::class);
    }
}
