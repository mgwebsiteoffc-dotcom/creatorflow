<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class CampaignReference extends Model
{
    protected $fillable = [
        'campaign_id', 'uploaded_by', 'kind', 'title',
        'disk', 'path', 'mime', 'size_bytes', 'url', 'note',
    ];

    protected $casts = [
        'size_bytes' => 'integer',
    ];

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function displayUrl(): ?string
    {
        if ($this->kind === 'link') {
            return $this->url;
        }
        if (! $this->path) {
            return null;
        }
        try {
            return Storage::disk($this->disk ?: 'public')->url($this->path);
        } catch (\Throwable) {
            return null;
        }
    }

    public function isImage(): bool
    {
        return str_starts_with((string) $this->mime, 'image/');
    }

    public function isVideo(): bool
    {
        return str_starts_with((string) $this->mime, 'video/');
    }

    public function humanSize(): string
    {
        $b = (int) $this->size_bytes;
        if ($b <= 0) {
            return '';
        }
        if ($b < 1024 * 1024) {
            return number_format($b / 1024, 1).' KB';
        }
        return number_format($b / 1024 / 1024, 1).' MB';
    }

    public function icon(): string
    {
        if ($this->kind === 'link') {
            return '🔗';
        }
        if ($this->isImage()) {
            return '🖼️';
        }
        if ($this->isVideo()) {
            return '🎬';
        }
        return match ($this->mime) {
            'application/pdf' => '📕',
            'application/zip' => '🗜️',
            default => '📎',
        };
    }
}
