<?php

namespace App\Domains\AI;

use App\Domains\AI\Contracts\AiProvider;
use App\Domains\AI\Contracts\AiResponse;
use App\Models\AiRun;
use App\Models\Workspace;

/**
 * Application-facing AI facade. Wraps the configured provider with logging,
 * cost tracking, structured output and workspace-level usage records.
 */
class AiGateway
{
    public function __construct(protected AiProvider $provider) {}

    /**
     * @param  array<int, array{role: string, content: string}>  $messages
     * @param  array<string, mixed>  $options
     */
    public function complete(
        string $task,
        array $messages,
        array $options = [],
        Workspace|int|null $workspace = null,
        ?string $subjectType = null,
        ?int $subjectId = null,
    ): AiResponse {
        $workspaceId = $workspace instanceof Workspace ? $workspace->id : $workspace;
        $started = microtime(true);

        $run = AiRun::create([
            'workspace_id' => $workspaceId,
            'subject_type' => $subjectType ?? 'generic',
            'subject_id' => $subjectId ?? 0,
            'task' => $task,
            'model' => $options['model'] ?? config('creatorplex.ai.openai.model', 'unknown'),
            'prompt' => json_encode($messages),
            'status' => 'running',
        ]);

        try {
            $response = $this->provider->complete($messages, $options);
            $run->markCompleted(
                response: $response->text,
                tokensIn: $response->tokensIn,
                tokensOut: $response->tokensOut,
                costCents: $this->estimateCostCents($response->tokensIn, $response->tokensOut),
                latencyMs: (int) ((microtime(true) - $started) * 1000),
            );

            return $response;
        } catch (\Throwable $e) {
            $run->update(['status' => 'failed', 'response' => $e->getMessage()]);
            throw $e;
        }
    }

    public function embed(string|array $inputs): array
    {
        return $this->provider->embed((array) $inputs);
    }

    /**
     * Cosine similarity between two equal-length numeric vectors.
     *
     * @param  array<int, float>  $a
     * @param  array<int, float>  $b
     */
    public function cosineSimilarity(array $a, array $b): float
    {
        if (count($a) !== count($b)) {
            return 0.0;
        }

        $dot = 0.0;
        $normA = 0.0;
        $normB = 0.0;

        for ($i = 0, $n = count($a); $i < $n; $i++) {
            $dot += $a[$i] * $b[$i];
            $normA += $a[$i] * $a[$i];
            $normB += $b[$i] * $b[$i];
        }

        if ($normA == 0 || $normB == 0) {
            return 0.0;
        }

        return $dot / (sqrt($normA) * sqrt($normB));
    }

    protected function estimateCostCents(int $in, int $out): int
    {
        // Rough gpt-4o-mini estimate: $0.15 / 1M in, $0.60 / 1M out.
        return (int) round(($in * 0.00000015 + $out * 0.0000006) * 100);
    }
}
