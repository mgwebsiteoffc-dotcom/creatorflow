<?php

namespace App\Domains\Matching;

use App\Models\Campaign;
use App\Models\CampaignCreatorMatch;
use App\Models\Creator;
use Illuminate\Support\Collection;

/**
 * Builds or refreshes the ranked candidate list for a campaign.
 *
 * Candidates are selected by a cheap pre-filter (niche, availability,
 * campaign type) and then scored by MatchCreatorForCampaign. The top N are
 * persisted to campaign_creator_matches for review and invitation.
 */
class GenerateMatches
{
    public function __construct(protected MatchCreatorForCampaign $scorer) {}

    /**
     * @return \Illuminate\Support\Collection<int, CampaignCreatorMatch>
     */
    public function run(Campaign $campaign, int $limit = 100): Collection
    {
        $candidates = Creator::active()
            ->where(function ($q) use ($campaign) {
                $q->where('accepts_barter', true)
                    ->orWhere('accepts_paid', true)
                    ->orWhere('accepts_affiliate', true);
            })
            ->where('fraud_risk', '<', 40)
            ->with(['nicheRows', 'socialAccounts'])
            ->inRandomOrder()
            ->take(max($limit * 3, 200))
            ->get();

        $scored = $candidates
            ->map(fn (Creator $creator) => array_merge(
                ['creator' => $creator],
                $this->scorer->score($campaign, $creator)
            ))
            ->sortByDesc('score')
            ->take($limit);

        $matches = collect();

        foreach ($scored as $row) {
            if ($row['score'] < 35) {
                continue;
            }

            $matches->push(CampaignCreatorMatch::updateOrCreate(
                ['campaign_id' => $campaign->id, 'creator_id' => $row['creator']->id],
                [
                    'score' => $row['score'],
                    'reasons' => $row['reasons'],
                    'predicted_performance' => $row['predicted'],
                    'status' => 'candidate',
                ]
            ));
        }

        return $matches;
    }
}
