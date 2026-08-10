<?php

namespace App\Events;

use App\Models\Campaign;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CampaignLaunched
{
    use Dispatchable, SerializesModels;

    public function __construct(public int $campaignId) {}

    public function campaign(): Campaign
    {
        return Campaign::findOrFail($this->campaignId);
    }
}
