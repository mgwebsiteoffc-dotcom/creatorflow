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
        $criteria = (array) ($campaign->audience_criteria ?? []);

        $query = Creator::active()
            ->where(function ($q) use ($campaign) {
                $q->where('accepts_barter', true)
                    ->orWhere('accepts_paid', true)
                    ->orWhere('accepts_affiliate', true);
            })
            ->where('fraud_risk', '<', 40)
            ->with(['nicheRows', 'socialAccounts']);

        // Apply audience filters (cities, tiers, gender, age, languages, follower/ER bounds).
        $query
            ->inCities($criteria['cities'] ?? [])
            ->inTiers($criteria['tiers'] ?? [])
            ->genderIn($criteria['genders'] ?? [])
            ->ageIn($criteria['age_ranges'] ?? [])
            ->languageIn($criteria['languages'] ?? []);

        if (! empty($criteria['min_followers'])) {
            $query->where('follower_count_total', '>=', (int) $criteria['min_followers']);
        }
        if (! empty($criteria['max_followers'])) {
            $query->where('follower_count_total', '<=', (int) $criteria['max_followers']);
        }
        if (! empty($criteria['min_engagement'])) {
            $query->where('engagement_rate', '>=', (float) $criteria['min_engagement']);
        }
        if (! empty($criteria['audience_gender']) && ! empty($criteria['audience_min_pct'])) {
            $col = $criteria['audience_gender'] === 'female' ? 'audience_female_pct' : 'audience_male_pct';
            if (\Illuminate\Support\Facades\Schema::hasColumn('creators', $col)) {
                $query->where($col, '>=', (int) $criteria['audience_min_pct']);
            }
        }

        $candidates = $query
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
