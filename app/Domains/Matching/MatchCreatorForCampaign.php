<?php

namespace App\Domains\Matching;

use App\Domains\AI\AiGateway;
use App\Models\Campaign;
use App\Models\Creator;
use App\Models\CreatorNiche;

/**
 * Scores a single creator against a campaign.
 *
 * The score blends four signals:
 *   - niche overlap           (0-100, weight 35)
 *   - audience/engagement fit (0-100, weight 25)
 *   - performance track record(0-100, weight 25)
 *   - risk (inverted fraud)   (0-100, weight 15)
 *
 * It also computes a lightweight semantic similarity between the campaign
 * brief and the creator's bio/niches using embeddings when an AI provider is
 * configured. The result is human-readable "reasons" for transparency.
 */
class MatchCreatorForCampaign
{
    public function __construct(protected AiGateway $ai) {}

    /**
     * @return array{score: float, reasons: array<int, string>, predicted: array<string, float>}
     */
    public function score(Campaign $campaign, Creator $creator): array
    {
        $reasons = [];

        // 1. Niche overlap
        $creatorNiches = CreatorNiche::where('creator_id', $creator->id)->pluck('niche')->all();
        if (empty($creatorNiches) && is_array($creator->niches)) {
            $creatorNiches = $creator->niches;
        }

        $nicheScore = $this->nicheScore((string) $campaign->niche, $creatorNiches);
        if ($nicheScore > 70) {
            $reasons[] = "Specializes in {$campaign->niche}";
        }

        // 2. Engagement fit (sweet spot: 3–15% engagement, non-zero followers)
        $engagement = (float) $creator->engagement_rate;
        $engagementScore = $this->engagementScore($engagement, (int) $creator->follower_count_total);
        if ($engagement >= 4) {
            $reasons[] = sprintf('%.1f%% engagement rate', $engagement);
        }

        // 3. Performance / track record
        $performance = (float) $creator->performance_score;
        if ($performance > 75) {
            $reasons[] = 'High completion & quality history';
        }

        // 4. Risk
        $risk = (float) $creator->fraud_risk;
        $riskScore = max(0, 100 - $risk);
        if ($risk <= 10) {
            $reasons[] = 'Low fraud risk';
        }

        // 5. Semantic overlap (optional; falls back gracefully)
        $semantic = 50.0;
        try {
            $semantic = $this->semanticScore($campaign, $creator, $creatorNiches) * 100;
        } catch (\Throwable) {
            // Offline / provider unavailable — ignore.
        }

        $score = round(
            $nicheScore * 0.30 +
            $engagementScore * 0.20 +
            $performance * 0.20 +
            $riskScore * 0.15 +
            $semantic * 0.15,
            2
        );

        if (count($reasons) === 0) {
            $reasons[] = 'Reasonable all-around fit';
        }

        $predicted = [
            'acceptance_probability' => $this->predictedAcceptance($score),
            'expected_content_assets' => $score > 70 ? 1.2 : 0.8,
            'expected_revenue_multiplier' => round(0.8 + ($score / 100) * 2.5, 2),
        ];

        return [
            'score' => min(99.9, max(0, $score)),
            'reasons' => array_slice($reasons, 0, 4),
            'predicted' => $predicted,
        ];
    }

    /**
     * @param  array<int, string>  $creatorNiches
     */
    protected function nicheScore(string $campaignNiche, array $creatorNiches): float
    {
        if ($campaignNiche === '' || empty($creatorNiches)) {
            return 40.0;
        }

        $campaign = strtolower($campaignNiche);

        foreach ($creatorNiches as $niche) {
            $niche = strtolower((string) $niche);

            if ($niche === $campaign) {
                return 100.0;
            }

            similar_text($campaign, $niche, $percent);
            if ($percent > 70) {
                return 85.0;
            }

            if (str_contains($campaign, $niche) || str_contains($niche, $campaign)) {
                return 80.0;
            }
        }

        return 30.0;
    }

    protected function engagementScore(float $engagement, int $followers): float
    {
        if ($followers < 1000) {
            return 40.0;
        }

        return match (true) {
            $engagement >= 8 => 95.0,
            $engagement >= 5 => 88.0,
            $engagement >= 3 => 78.0,
            $engagement >= 1.5 => 60.0,
            $engagement > 0 => 40.0,
            default => 20.0,
        };
    }

    /**
     * @param  array<int, string>  $creatorNiches
     */
    protected function semanticScore(Campaign $campaign, Creator $creator, array $creatorNiches): float
    {
        $campaignText = trim(($campaign->title ?? '').' '.($campaign->niche ?? '').' '.strip_tags((string) ($campaign->brief ?? '')));
        $creatorText = trim(($creator->display_name ?? '').' '.($creator->bio ?? '').' '.implode(' ', $creatorNiches));

        if ($campaignText === '' || $creatorText === '') {
            return 0.5;
        }

        [$cVec, $crVec] = $this->ai->embed([$campaignText, $creatorText]);

        return $this->ai->cosineSimilarity($cVec, $crVec);
    }

    protected function predictedAcceptance(float $score): float
    {
        return round(min(0.85, 0.15 + ($score / 100) * 0.65), 2);
    }
}
