<?php

namespace App\Domains\Content\Actions;

use App\Events\ContentSubmitted as ContentSubmittedEvent;
use App\Jobs\RunAiContentReview;
use App\Models\AppNotification;
use App\Models\CampaignAssignment;
use App\Models\ContentSubmission;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SubmitContent
{
    /**
     * @param  array<string, mixed>  $attributes
     */
    public function handle(CampaignAssignment $assignment, string $type, UploadedFile|string $file, array $attributes = []): ContentSubmission
    {
        if ($file instanceof UploadedFile) {
            $extension = $file->getClientOriginalExtension() ?: match ($type) {
                'image' => 'jpg',
                'video' => 'mp4',
                default => 'bin',
            };

            $path = $file->storeAs(
                "ugc/assignment-{$assignment->id}",
                Str::uuid().'.'.$extension,
                ['disk' => config('filesystems.default', 'local')]
            );
        } else {
            $path = $file;
        }

        $submission = ContentSubmission::create([
            'assignment_id' => $assignment->id,
            'creator_id' => $assignment->creator_id,
            'campaign_id' => $assignment->campaign_id,
            'type' => $type,
            'disk' => config('filesystems.default', 'local'),
            'path' => $path,
            'caption' => $attributes['caption'] ?? null,
            'external_post_url' => $attributes['external_post_url'] ?? null,
            'metadata' => $attributes['metadata'] ?? null,
            'status' => 'submitted',
            'submitted_at' => now(),
        ]);

        $assignment->update([
            'status' => 'submitted',
            'campaign_product_id' => $assignment->campaign_product_id,
        ]);

        $assignment->campaignProduct?->increment('content_received_count');

        RunAiContentReview::dispatch($submission->id);

        event(new ContentSubmittedEvent($submission->id));

        // Notify every brand user in the campaign's workspace.
        $campaign = $assignment->campaign;
        foreach ($campaign->workspace?->users ?? [] as $brandUser) {
            AppNotification::notifyUser($brandUser->id, 'content.submitted', [
                'title'       => 'New content submitted',
                'body'        => ($assignment->creator->display_name ?? 'A creator')." submitted content for {$campaign->title}.",
                'url'         => route('brand.assignments.show', $assignment),
                'campaign_id' => $campaign->id,
                'assignment_id' => $assignment->id,
                'submission_id' => $submission->id,
            ]);
        }

        return $submission;
    }
}
