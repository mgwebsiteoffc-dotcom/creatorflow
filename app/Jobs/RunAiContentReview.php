<?php

namespace App\Jobs;

use App\Domains\AI\AiGateway;
use App\Models\ContentReview;
use App\Models\ContentSubmission;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class RunAiContentReview implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 2;

    public function __construct(public int $submissionId) {}

    public function handle(AiGateway $ai): void
    {
        $submission = ContentSubmission::with(['campaign.workspace', 'creator', 'assignment'])->findOrFail($this->submissionId);

        if ($submission->ai_score !== null) {
            return; // Idempotent.
        }

        $response = $ai->complete(
            task: 'content_review',
            messages: [
                ['role' => 'system', 'content' => 'You review creator content against campaign briefs. Respond with JSON only.'],
                ['role' => 'user', 'content' => json_encode([
                    'campaign' => $submission->campaign->title,
                    'brief' => $submission->campaign->brief,
                    'caption' => $submission->caption,
                    'type' => $submission->type,
                ])],
            ],
            options: [
                'json' => true,
                'task' => 'content_review',
                'seed' => [],
            ],
            workspace: $submission->campaign->workspace,
            subjectType: 'content_submission',
            subjectId: $submission->id,
        );

        $result = $response->json() ?? [];
        $score = isset($result['score']) ? (float) $result['score'] * 10 : 75.0;
        $passed = (bool) ($result['passed'] ?? true);

        $submission->update([
            'ai_score' => $score,
            'ai_feedback' => $result['feedback'] ?? null,
            'ai_flags' => $result['flags'] ?? null,
            'status' => $passed && $score >= 70 ? 'in_review' : 'changes_requested',
        ]);

        ContentReview::create([
            'content_submission_id' => $submission->id,
            'reviewer_type' => 'ai',
            'decision' => $passed && $score >= 70 ? 'approved' : 'changes_requested',
            'comment' => is_string($result['feedback'] ?? null) ? $result['feedback'] : null,
            'ai_annotations' => $result,
            'created_at' => now(),
        ]);
    }
}
