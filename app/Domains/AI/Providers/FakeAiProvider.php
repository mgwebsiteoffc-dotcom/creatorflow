<?php

namespace App\Domains\AI\Providers;

use App\Domains\AI\Contracts\AiProvider;
use App\Domains\AI\Contracts\AiResponse;
use Illuminate\Support\Str;

/**
 * Deterministic, offline AI provider used by seeders, tests and local dev.
 *
 * It produces structured, realistic-looking campaign/match/content data based
 * on the input payload so the whole AI-powered workflow is demonstrable
 * without external API keys or network access.
 */
class FakeAiProvider implements AiProvider
{
    public function complete(array $messages, array $options = []): AiResponse
    {
        $task = $options['task'] ?? 'generic';
        $seed = $options['seed'] ?? [];

        $text = match ($task) {
            'analyze_store' => $this->analyzeStore($seed),
            'generate_campaign' => $this->generateCampaign($seed),
            'generate_brief' => $this->generateBrief($seed),
            'draft_message' => $this->draftMessage($seed),
            'content_review' => $this->reviewContent($seed),
            'fraud_check' => $this->fraudCheck($seed),
            'roi_prediction' => $this->predictRoi($seed),
            'creator_summary' => $this->creatorSummary($seed),
            default => 'Fake AI response for task: '.$task,
        };

        return new AiResponse(
            text: $text,
            tokensIn: 256,
            tokensOut: max(64, strlen($text) / 4),
            model: 'fake-ai-1',
            structured: json_decode($text, true),
        );
    }

    public function embed(array $inputs): array
    {
        // Deterministic pseudo-embeddings: hashed token counts produce a
        // stable vector. Good enough for demo/test matching; real cosine
        // similarity still works over these vectors.
        return array_map(function (string $input): array {
            $tokens = preg_split('/\W+/u', strtolower($input)) ?: [];
            $vector = array_fill(0, 64, 0.0);
            foreach ($tokens as $i => $token) {
                $hash = crc32($token) % 64;
                $vector[$hash] += 1.0;
                $vector[($hash + 3) % 64] += 0.5;
            }
            $norm = sqrt(array_sum(array_map(fn ($v) => $v * $v, $vector))) ?: 1;

            return array_map(fn ($v) => $v / $norm, $vector);
        }, $inputs);
    }

    protected function analyzeStore(array $seed): string
    {
        $niche = $seed['niche'] ?? 'Beauty & Skincare';

        return json_encode([
            'niche' => $niche,
            'sub_niches' => ['Clean Beauty', 'DTC Skincare'],
            'hero_product_ids' => $seed['hero_product_ids'] ?? [],
            'target_audience' => [
                'age' => '18-34',
                'gender' => 'women-heavy',
                'interests' => ['skincare', 'clean beauty', 'self-care'],
            ],
            'content_angles' => ['morning routine', 'before/after', 'unboxing', 'GRWM'],
            'confidence' => 0.86,
        ], JSON_PRETTY_PRINT);
    }

    protected function generateCampaign(array $seed): string
    {
        $niche = $seed['niche'] ?? 'Beauty & Skincare';
        $target = (int) ($seed['target_creators'] ?? 30);

        return json_encode([
            'title' => $seed['title'] ?? "{$niche} UGC Seeding Sprint",
            'type' => $seed['type'] ?? 'barter',
            'niche' => $niche,
            'summary' => 'A one-month UGC seeding campaign to generate authentic product content and drive attributed trial orders.',
            'content_types' => ['video', 'story'],
            'target_creators' => $target,
            'invite_pool_size' => (int) ceil($target / 0.30),
            'acceptance_rate_assumed' => 30.0,
            'objectives' => ['ugc', 'awareness'],
            'budget_total_cents' => (int) ($seed['budget_total_cents'] ?? 0),
            'predicted' => [
                'content_assets' => (int) round($target * 0.75),
                'reach' => $target * 14000,
                'attributed_orders' => (int) round($target * 0.8),
                'roi_low' => 1.8,
                'roi_high' => 3.4,
            ],
        ], JSON_PRETTY_PRINT);
    }

    protected function generateBrief(array $seed): string
    {
        $product = $seed['product_title'] ?? 'the product';

        return "# Campaign Brief\n\n".
            "## About the brand\nWe're a DTC brand shipping {$product}.\n\n".
            "## Goal\nCreate authentic UGC that showcases real results and drives trial.\n\n".
            "## Content requirements\n- 15–45s vertical video\n- Show the product in use within the first 3 seconds\n- Mention the key benefit naturally\n- Include your unique discount code\n\n".
            "## Do\n- Use natural lighting and honest reactions\n- Tag the brand and use #ad\n\n## Don't\n- Make unverified medical claims\n- Use competitor products on camera\n";
    }

    protected function draftMessage(array $seed): string
    {
        $creator = $seed['creator_name'] ?? 'there';
        $product = $seed['product_title'] ?? 'our product';

        return "Hey {$creator}! We loved your recent content — your style feels like a perfect fit for {$product}. ".
            "We're running a seeding campaign: free product + a unique code, with a chance to be featured. Interested?";
    }

    protected function reviewContent(array $seed): string
    {
        return json_encode([
            'score' => rand(72, 96) / 10,
            'passed' => true,
            'feedback' => [
                'Product shown within first 3 seconds',
                'Brand mention detected',
                'Lighting and audio are clear',
            ],
            'flags' => [],
            'recommendation' => 'approve',
        ], JSON_PRETTY_PRINT);
    }

    protected function fraudCheck(array $seed): string
    {
        return json_encode([
            'fraud_risk' => rand(2, 25),
            'fake_follower_pct' => rand(1, 15),
            'signals' => ['healthy comment diversity', 'organic growth curve'],
            'verdict' => 'low_risk',
        ], JSON_PRETTY_PRINT);
    }

    protected function predictRoi(array $seed): string
    {
        $target = (int) ($seed['target_creators'] ?? 30);

        return json_encode([
            'predicted_revenue_cents' => $target * 3200,
            'predicted_content_assets' => (int) round($target * 0.75),
            'roi_p25' => 1.6,
            'roi_p50' => 2.3,
            'roi_p75' => 3.1,
            'confidence' => 0.72,
        ], JSON_PRETTY_PRINT);
    }

    protected function creatorSummary(array $seed): string
    {
        $niches = is_array($seed['niches'] ?? null) ? implode(', ', $seed['niches']) : 'lifestyle';

        return Str::limit("Creator specializing in {$niches}, with strong engagement and a track record of authentic UGC for DTC brands.", 200);
    }
}
