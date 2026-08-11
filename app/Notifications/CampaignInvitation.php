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
        $creator = Creator::find($this->creatorId);

        return (new MailMessage)
            ->subject("You're invited: {$campaign?->title}")
            ->greeting("Hi {$creator?->display_name},")
            ->line("{$campaign?->workspace->name} invited you to a creator campaign.")
            ->action('View invitation', route('creator.invitations'))
            ->line('Free product, fair pay, and clear briefs — that\'s CreatorPlex.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'campaign_id' => $this->campaignId,
            'creator_id' => $this->creatorId,
            'message' => 'You have a new campaign invitation.',
            'url' => route('creator.invitations'),
        ];
    }
}
