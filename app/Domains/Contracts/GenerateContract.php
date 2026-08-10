<?php

namespace App\Domains\Contracts;

use App\Models\CampaignAssignment;
use App\Models\Contract;
use Illuminate\Support\Number;

class GenerateContract
{
    public function handle(CampaignAssignment $assignment): Contract
    {
        $campaign = $assignment->campaign;
        $creator = $assignment->creator;
        $workspace = $campaign->workspace;
        $product = $assignment->campaignProduct->product;

        $fee = Number::currency($assignment->fee_cents / 100, $campaign->budget_currency);

        $body = $this->template(
            brand: $workspace->name,
            creator: $creator->display_name,
            campaign: $campaign->title,
            product: $product->title,
            fee: $fee,
            type: $campaign->type,
            rights: $campaign->usage_rights,
        );

        return Contract::create([
            'workspace_id' => $workspace->id,
            'campaign_id' => $campaign->id,
            'creator_id' => $creator->id,
            'title' => "Agreement: {$campaign->title}",
            'body' => $body,
            'usage_rights' => $campaign->usage_rights,
            'fee_cents' => $assignment->fee_cents,
            'status' => 'sent',
            'expires_at' => now()->addDays(7),
        ]);
    }

    /**
     * @param  array<string, mixed>|null  $rights
     */
    protected function template(
        string $brand,
        string $creator,
        string $campaign,
        string $product,
        string $fee,
        string $type,
        ?array $rights,
    ): string {
        $rightsText = $rights
            ? json_encode($rights, JSON_PRETTY_PRINT)
            : 'Organic social usage for 90 days. Whitelisting/paid usage requires separate written consent.';

        return <<<MD
# Creator Agreement: {$campaign}

This agreement is between **{$brand}** ("Brand") and **{$creator}** ("Creator").

## Campaign
- Product: **{$product}**
- Type: **{$type}**
- Creator fee: **{$fee}** (released on content approval)
- The Creator will receive the product at no cost for campaign use.

## Deliverables
The Creator agrees to produce and submit content per the campaign brief,
including required brand mentions, disclosures (#ad / #gifted) and the
unique tracking/discount code provided.

## Usage Rights
```
{$rightsText}
```

## Payment
Fees are released via Stripe Connect after content is approved, following
a 7-day clawback window. Barter product value is not a cash payment.

## Acceptance
Signing digitally confirms both parties agree to these terms.
MD;
    }
}
