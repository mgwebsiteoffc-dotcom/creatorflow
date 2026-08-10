<?php

namespace App\Events;

use App\Models\CampaignInvitation;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CreatorAcceptedInvitation
{
    use Dispatchable, SerializesModels;

    public function __construct(public int $invitationId) {}

    public function invitation(): CampaignInvitation
    {
        return CampaignInvitation::with(['creator', 'campaign'])->findOrFail($this->invitationId);
    }
}
