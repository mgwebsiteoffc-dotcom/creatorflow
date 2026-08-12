<?php

namespace App\Domains\Content\Actions;

use App\Domains\Billing\Actions\ReleasePayout;
use App\Models\AppNotification;
use App\Models\ContentReview;
use App\Models\ContentSubmission;
use Illuminate\Support\Facades\DB;

class ApproveContent
{
    public function __construct(protected ReleasePayout $releasePayout) {}

    public function handle(ContentSubmission $submission, ?int $userId = null): ContentSubmission
    {
        return DB::transaction(function () use ($submission, $userId) {
            $submission->approve();

            ContentReview::create([
                'content_submission_id' => $submission->id,
                'reviewer_type' => 'brand',
                'reviewer_id' => $userId,
                'decision' => 'approved',
                'comment' => 'Approved by brand',
                'created_at' => now(),
            ]);

            // Release cash payout (if any) for the assignment.
            $this->releasePayout->handle($submission);

            $assignment = $submission->assignment;
            if ($assignment->submissions()->where('status', 'approved')->exists()) {
                $assignment->update(['status' => 'completed']);
            }

            AppNotification::notifyCreator($assignment->creator_id, 'content.approved', [
                'title'         => 'Your content was approved 🎉',
                'body'          => 'The brand approved your submission. Payout is on its way.',
                'url'           => route('creator.assignments.show', $assignment),
                'assignment_id' => $assignment->id,
                'submission_id' => $submission->id,
            ]);

            // Fire email + WhatsApp via templates (in-app is already handled above).
            \App\Support\NotifyEvent::fire('creator.content.approved', $assignment->creator, [
                'creator_name'   => $assignment->creator->display_name,
                'brand_name'     => $assignment->campaign->workspace->name ?? '',
                'campaign_title' => $assignment->campaign->title ?? '',
                'link'           => route('creator.assignments.show', $assignment),
            ]);

            return $submission->fresh();
        });
    }
}
