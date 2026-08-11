<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CreatorImport extends Model
{
    protected $fillable = [
        'user_id', 'source', 'filename', 'total_rows', 'imported_rows',
        'failed_rows', 'errors', 'status',
    ];

    protected $casts = ['errors' => 'array'];

    public function user(): BelongsTo { return $this->belongsTo(User::class); }
}
