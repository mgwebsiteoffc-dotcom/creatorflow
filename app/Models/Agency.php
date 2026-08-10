<?php

namespace App\Models;

use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Agency extends Model
{
    use HasUuid;

    protected $fillable = ['uuid', 'name', 'plan', 'owner_id', 'settings'];

    protected $casts = ['settings' => 'array'];

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function workspaces(): HasMany
    {
        return $this->hasMany(Workspace::class);
    }
}
