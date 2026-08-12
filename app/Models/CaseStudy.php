<?php

namespace App\Models;

use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CaseStudy extends Model
{
    use HasUuid, SoftDeletes;

    protected $fillable = [
        'uuid','workspace_id','slug','brand_name','brand_logo_path','cover_image_path','hero_video_url',
        'industry','city','campaign_type','headline','subheadline','summary','challenge','solution','results',
        'metrics','gallery','quote','quote_author','quote_role','seo','published_at','featured','position',
    ];
    protected $casts = [
        'metrics'      => 'array',
        'gallery'      => 'array',
        'seo'          => 'array',
        'featured'     => 'boolean',
        'published_at' => 'datetime',
    ];

    public function scopePublished($q) { return $q->whereNotNull('published_at')->where('published_at', '<=', now()); }
    public function isPublished(): bool { return $this->published_at && $this->published_at->isPast(); }

    public function metaTitle(): string       { return $this->seo['meta_title']       ?? ($this->brand_name.' · '.$this->headline.' — CreatorPlex case study'); }
    public function metaDescription(): string { return $this->seo['meta_description'] ?? \Illuminate\Support\Str::limit(strip_tags($this->summary ?: $this->subheadline ?: ''), 155); }
    public function ogImage(): ?string        { return $this->seo['og_image'] ?? $this->cover_image_path; }
}
