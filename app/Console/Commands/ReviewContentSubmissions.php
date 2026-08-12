<?php

namespace App\Console\Commands;

use App\Domains\AI\AiGateway;
use App\Models\ContentReview;
use App\Models\ContentSubmission;
use App\Models\PlatformSetting;
use Illuminate\Console\Command;

class ReviewContentSubmissions extends Command
{
    protected $signature = 'content:review
                            {--limit=25 : Max submissions to review per run}
                            {--force : Ignore the feature flag}';
    protected $description = 'Auto-score newly submitted UGC against the campaign brief. Writes ContentReview rows for humans to confirm.';

    public function handle(AiGateway $ai): int
    {
        if (! $this->option('force') && ! PlatformSetting::feature('auto_content_review')) {
            $this->warn('Feature flag "auto_content_review" is off. Enable it in Admin → Integrations → Feature flags.');
            return self::SUCCESS;
        }

        $submissions = ContentSubmission::query()
            ->whereIn('status', ['submitted'])
            ->whereDoesntHave('reviews', fn ($q) => $q->where('reviewer_type', 'ai'))
            ->with('assignment.campaign')
            ->orderBy('created_at')
            ->take((int) $this->option('limit'))
            ->get();

        $done = 0;
        foreach ($submissions as $sub) {
            try {
                $brief = strip_tags((string) ($sub->assignment->campaign->brief ?? ''));
                $response = $ai->complete(
                    task: 'content_review',
                    messages: [
                        ['role' => 'system', 'content' => 'You review UGC videos + posts against a brand brief. Return JSON: {score:0-10, passed:bool, feedback:[…], flags:[…], recommendation:"approve"|"changes"|"reject"}.'],
                        ['role' => 'user',   'content' => "Brief:\n{$brief}\n\nSubmission: {$sub->type} — {$sub->caption} — {$sub->url}"],
                    ],
                    options: ['json' => true, 'task' => 'content_review', 'seed' => ['submission_id' => $sub->id]],
                );
                $parsed = is_array($response->structured ?? null) ? $response->structured : json_decode($response->text, true);
                if (! is_array($parsed)) continue;

                ContentReview::create([
                    'content_submission_id' => $sub->id,
                    'reviewer_type'         => 'ai',
                    'reviewer_id'           => 0,
                    'decision'              => $parsed['recommendation'] ?? 'changes',
                    'comment'               => is_array($parsed['feedback'] ?? null) ? implode(' · ', $parsed['feedback']) : ($parsed['feedback'] ?? null),
                    'ai_annotations'        => $parsed,
                    'created_at'            => now(),
                ]);
                $done++;
            } catch (\Throwable $e) {
                $this->warn("Skipped submission #{$sub->id}: {$e->getMessage()}");
            }
        }

        $this->info("✓ Reviewed {$done} submissions.");
        return self::SUCCESS;
    }
}
