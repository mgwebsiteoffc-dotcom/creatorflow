<?php

namespace App\Jobs;

use App\Domains\AI\AiGateway;
use App\Models\Campaign;
use App\Models\CampaignCreatorMatch;
use App\Models\CampaignInvitation;
use App\Models\Creator;
use App\Notifications\CampaignInvitation as InvitationNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class SendCampaignInvitations implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public function __construct(public int $campaignId, public int $limit) {}

    public function handle(AiGateway $ai): void
    {
        $campaign = Campaign::with(['products.product.variants', 'workspace'])->findOrFail($this->campaignId);

        /** @var Collection<int, CampaignCreatorMatch> $matches */
        $matches = $campaign->matches()
            ->where('status', 'candidate')
            ->orderByDesc('score')
            ->take($this->limit)
            ->get();

        $invited = 0;

        foreach ($matches as $match) {
            $creator = $match->creator;

            if (! $creator->acceptsCampaignType($campaign->type)) {
                continue;
            }

            $message = $this->draftMessage($ai, $campaign, $creator);

            CampaignInvitation::create([
                'campaign_id' => $campaign->id,
                'creator_id' => $creator->id,
                'campaign_product_id' => $this->pickProductForCreator($campaign, $creator)?->id,
                'channel' => 'in_app',
                'message' => $message,
                'ai_variant' => 'default',
                'status' => 'sent',
                'sent_at' => now(),
                'expires_at' => now()->addDays(7),
            ]);

            $match->update(['status' => 'invited', 'invited_at' => now()]);

            $creator->notify(new InvitationNotification($campaign->id, $creator->id));

            \App\Support\NotifyEvent::fire('creator.invited', $creator, [
                'creator_name'   => $creator->display_name,
                'brand_name'     => $campaign->workspace->name,
                'campaign_title' => $campaign->title,
                'message'        => \Illuminate\Support\Str::limit((string) $message, 240),
                'link'           => url('/creator/invitations'),
            ]);

            $invited++;
        }

        // Promote overflow candidates into a waitlist.
        $overflow = $campaign->matches()
            ->where('status', 'candidate')
            ->orderByDesc('score')
            ->take((int) ceil($this->limit * 0.3))
            ->get();

        $position = 1;
        foreach ($overflow as $candidate) {
            $campaign->waitlist()->updateOrCreate(
                ['creator_id' => $candidate->creator_id],
                ['position' => $position++, 'status' => 'waiting']
            );
            $candidate->update(['status' => 'waitlisted']);
        }
    }

    protected function draftMessage(AiGateway $ai, Campaign $campaign, Creator $creator): string
    {
        $product = $campaign->products->first()?->product;

        $response = $ai->complete(
            task: 'draft_message',
            messages: [
                ['role' => 'system', 'content' => 'Write a short, friendly creator outreach message.'],
                ['role' => 'user', 'content' => "Brand: {$campaign->workspace->name}; Product: ".($product->title ?? 'our product')."; Creator: {$creator->display_name}"],
            ],
            options: ['task' => 'draft_message', 'seed' => [
                'creator_name' => $creator->display_name,
                'product_title' => $product->title ?? 'our product',
            ]],
            workspace: $campaign->workspace,
            subjectType: 'campaign',
            subjectId: $campaign->id,
        );

        return $response->text;
    }

    protected function pickProductForCreator(Campaign $campaign, Creator $creator)
    {
        // Simple allocation: pick the campaign product with the most open slots.
        return $campaign->products
            ->sortByDesc(fn ($cp) => $cp->openSlots())
            ->first();
    }
}
