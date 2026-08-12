<?php

namespace App\Notifications;

use App\Models\Campaign;
use App\Models\Creator;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CampaignInvitation extends Notification
{
    use Queueable;

    public function __construct(public int $campaignId, public int $creatorId) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function routeNotificationForMail(object $notifiable): ?string
    {
        return $notifiable->email ?? null;
    }

    public function toMail(object $notifiable): MailMessage
    {
        $campaign = Campaign::find($this->campaignId);
        $creator  = Creator::find($this->creatorId);

        // Find the actual invitation row so we can deep-link with its UUID.
        // The magic link auto-logs in the creator (if the email matches) and
        // drops them straight on /creator/invitations with the row highlighted.
        $inv = \App\Models\CampaignInvitation::where('campaign_id', $this->campaignId)
            ->where('creator_id', $this->creatorId)
            ->latest('id')
            ->first();

        $link = $inv
            ? route('invite.open', ['uuid' => $inv->uuid])
            : route('creator.invitations');

        return (new MailMessage)
            ->subject("You're invited: {$campaign?->title}")
            ->greeting("Hi {$creator?->display_name},")
            ->line("{$campaign?->workspace->name} invited you to a creator campaign.")
            ->action('Open invitation', $link)
            ->line('Free product, fair pay, and clear briefs — that\'s CreatorPlex.');
    }

    public function toArray(object $notifiable): array
    {
        $inv = \App\Models\CampaignInvitation::where('campaign_id', $this->campaignId)
            ->where('creator_id', $this->creatorId)
            ->latest('id')
            ->first();

        return [
            'campaign_id' => $this->campaignId,
            'creator_id'  => $this->creatorId,
            'message'     => 'You have a new campaign invitation.',
            'url'         => $inv ? route('invite.open', ['uuid' => $inv->uuid]) : route('creator.invitations'),
        ];
    }
}
