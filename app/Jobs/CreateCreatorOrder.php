<?php

namespace App\Jobs;

use App\Domains\Commerce\Channels\ChannelRegistry;
use App\Domains\Commerce\Channels\Contracts\ChannelOrder;
use App\Models\CampaignAssignment;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CreateCreatorOrder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public function __construct(public int $assignmentId) {}

    public function handle(ChannelRegistry $channels): void
    {
        $assignment = CampaignAssignment::with([
            'campaign.workspace', 'campaignProduct.product.variants',
            'campaignProduct.variant', 'creator.preferences',
        ])->findOrFail($this->assignmentId);

        if ($assignment->channel_order_id) {
            return; // Idempotent: already created.
        }

        $workspace = $assignment->campaign->workspace;
        $channel = $channels->forWorkspace($workspace);

        $channelOrder = $channel->createCreatorOrder($assignment);

        // For the Shopify adapter the order already exists remotely; for the
        // manual adapter it was created in-process. Normalize into a local
        // Order record linked to the assignment.
        $order = $this->persistOrder($assignment, $channelOrder);

        $assignment->update([
            'channel_order_id' => $order->id,
            'status' => 'order_created',
        ]);

        // Decrement available inventory so we don't over-promise product.
        $variant = $assignment->campaignProduct->variant;
        if ($variant && $variant->inventory_qty > 0) {
            $variant->decrement('inventory_qty');
        }    }

    protected function persistOrder(CampaignAssignment $assignment, ChannelOrder $channelOrder): Order
    {
        $workspace = $assignment->campaign->workspace;

        $existing = Order::where('external_id', $channelOrder->externalId)
            ->where('workspace_id', $workspace->id)
            ->first();

        if ($existing) {
            return $existing;
        }

        $product = $assignment->campaignProduct->product;
        $variant = $assignment->campaignProduct->variant;
        $price = $variant?->price_cents ?? $product->priceCents();

        $order = Order::create([
            'workspace_id' => $workspace->id,
            'channel_id' => $workspace->channels()->where('type', '!=', 'manual')->value('id')
                ?: $workspace->channels()->first()?->id,
            'creator_id' => $assignment->creator_id,
            'external_id' => $channelOrder->externalId,
            'order_number' => $channelOrder->orderNumber,
            'subtotal_cents' => $price,
            'total_discount_cents' => $price,
            'total_cents' => 0,
            'currency' => $channelOrder->currency,
            'status' => $channelOrder->status,
            'shipping_address' => $assignment->creator->preferences?->shipping_address,
            'placed_at' => now(),
            'raw_payload' => $channelOrder->raw,
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'variant_id' => $variant?->id,
            'title' => $product->title,
            'sku' => $variant?->sku,
            'quantity' => 1,
            'price_cents' => $price,
            'total_discount_cents' => $price,
        ]);

        return $order;
    }
}
