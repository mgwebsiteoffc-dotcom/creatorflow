<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class BlogPost extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'author_id', 'slug', 'title', 'category', 'excerpt', 'body',
        'cover_gradient', 'cover_image_path',
        'meta_title', 'meta_description', 'canonical_url',
        'faq_json', 'read_minutes', 'is_published', 'published_at',
    ];

    protected $casts = [
        'faq_json' => 'array',
        'is_published' => 'boolean',
        'published_at' => 'datetime',
    ];

    public function author(): BelongsTo { return $this->belongsTo(User::class, 'author_id'); }

    public function scopePublished($q)
    {
        return $q->where('is_published', true)
                 ->where(fn ($qq) => $qq->whereNull('published_at')->orWhere('published_at', '<=', now()));
    }

    public function metaTitle(): string
    {
        return $this->meta_title ?: $this->title;
    }

    public function metaDescription(): string
    {
        return $this->meta_description ?: str($this->excerpt ?: strip_tags($this->body))->limit(160);
    }
}
