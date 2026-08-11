<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HomepageItem extends Model
{
    protected $fillable = [
        'section', 'category', 'title', 'subtitle',
        'image_path', 'video_path', 'poster_path',
        'external_url', 'gradient', 'meta',
        'position', 'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'position'  => 'integer',
    ];

    public function scopeActive($q)     { return $q->where('is_active', true); }
    public function scopeSection($q, $s){ return $q->where('section', $s); }
}
