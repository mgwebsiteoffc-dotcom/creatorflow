<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContentReview extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'content_submission_id', 'reviewer_type', 'reviewer_id',
        'decision', 'comment', 'ai_annotations', 'created_at',
    ];

    protected $casts = [
        'ai_annotations' => 'array',
        'created_at' => 'datetime',
    ];

    public function submission(): BelongsTo
    {
        return $this->belongsTo(ContentSubmission::class, 'content_submission_id');
    }
}
