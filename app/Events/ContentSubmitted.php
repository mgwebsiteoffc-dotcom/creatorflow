<?php

namespace App\Events;

use App\Models\ContentSubmission;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ContentSubmitted
{
    use Dispatchable, SerializesModels;

    public function __construct(public int $submissionId) {}

    public function submission(): ContentSubmission
    {
        return ContentSubmission::findOrFail($this->submissionId);
    }
}
