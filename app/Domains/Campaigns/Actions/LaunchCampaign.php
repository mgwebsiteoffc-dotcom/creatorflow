<?php

namespace App\Domains\Campaigns\Actions;

use App\Domains\Matching\GenerateMatches;
use App\Events\CampaignLaunched as CampaignLaunchedEvent;
use App\Jobs\SendCampaignInvitations;
use App\Models\Campaign;

class LaunchCampaign
{
    public function __construct(protected GenerateMatches $generateMatches) {}

    public function handle(Campaign $campaign, bool $dispatchInvitations = true): Campaign
    {
        if ($campaign->isLaunched()) {
            return $campaign;
        }

        $campaign->update([
            'status' => 'matching',
            'launched_at' => now(),
        ]);

        // Generate (or refresh) AI-ranked creator candidates.
        $matches = $this->generateMatches->run(
            $campaign,
            limit: max($campaign->invite_pool_size, $campaign->target_creators * 3)
        );

        $campaign->update(['status' => 'inviting']);

        if ($dispatchInvitations) {
            // Push the top-N candidates into the invitation pipeline. The waitlist
            // captures the rest and is promoted as creators decline.
            SendCampaignInvitations::dispatch(
                $campaign->id,
                $campaign->invite_pool_size
            );
        }

        event(new CampaignLaunchedEvent($campaign->id));

        return $campaign->fresh('products', 'matches');
    }
}
