<?php

namespace App\Domains\Commerce\Channels;

use App\Domains\Commerce\Channels\Contracts\ChannelOrder;
use App\Domains\Commerce\Channels\Contracts\CommerceChannel;
use App\Domains\Commerce\Channels\Contracts\SyncResult;
use App\Models\CampaignAssignment;
use App\Models\Creator;
use App\Models\DiscountCode;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\SyncJob;
use App\Models\Workspace;
use Illuminate\Support\Str;

/**
 * Fallback channel for brands without a connected store. Products are
 * entered manually or imported from CSV; orders are tracked inside
 * CreatorPlex rather than pushed to an external platform.
 */
class ManualChannel implements CommerceChannel
{
    public function syncProducts(Workspace $workspace, SyncJob $job): SyncResult
    {
        // Manual/CSV products are written directly by their own importers;
        // there is nothing to pull.
        return new SyncResult();
    }

    public function syncInventory(Workspace $workspace, SyncJob $job): SyncResult
    {
        return new SyncResult();
    }

    public function syncOrders(Workspace $workspace, \DateTimeInterface $since, SyncJob $job): SyncResult
    {
        return new SyncResult();
    }

    public function createDiscountCode(Workspace $workspace, Product $product, Creator $creator, array $options): DiscountCode
    {
        $code = $options['code'] ?? 'CF-'.Str::upper(Str::random(8));

        return DiscountCode::create([
            'workspace_id' => $workspace->id,
            'campaign_id' => $options['campaign_id'],
            'creator_id' => $creator->id,
            'product_id' => $product->id,
            'code' => $code,
            'type' => $options['type'] ?? 'full_comp',
            'value' => (float) ($options['value'] ?? 100),
            'external_id' => 'manual_'.$code,
            'usage_limit' => (int) ($options['usage_limit'] ?? 1),
            'starts_at' => now(),
            'expires_at' => $options['expires_at'] ?? now()->addMonths(3),
            'status' => 'active',
        ]);
    }

    public function createCreatorOrder(CampaignAssignment $assignment): ChannelOrder
    {
        $workspace = $assignment->campaign->workspace;
        $channel = $workspace->channels()->firstOrCreate(
            ['type' => 'manual'],
            ['name' => 'Manual', 'status' => 'active']
        );

        $campaignProduct = $assignment->campaignProduct;
        $product = $campaignProduct->product;
        $variant = $campaignProduct->variant;
        $creator = $assignment->creator;

        $priceCents = $variant->price_cents ?? $product->priceCents();

        $order = Order::create([
            'workspace_id' => $workspace->id,
            'channel_id' => $channel->id,
            'creator_id' => $creator->id,
            'external_id' => 'manual_'.Str::uuid()->toString(),
            'order_number' => 'CF-'.Str::upper(Str::random(8)),
            'email' => $creator->email,
            'subtotal_cents' => $priceCents,
            'total_discount_cents' => $priceCents,
            'total_cents' => 0,
            'currency' => $workspace->currency,
            'status' => 'open',
            'shipping_address' => $creator->preferences?->shipping_address,
            'placed_at' => now(),
        ]);

        $order->items()->create([
            'product_id' => $product->id,
            'variant_id' => $variant?->id,
            'title' => $product->title,
            'sku' => $variant?->sku,
            'quantity' => 1,
            'price_cents' => $priceCents,
            'total_discount_cents' => $priceCents,
        ]);

        $assignment->update(['channel_order_id' => $order->id]);

        return new ChannelOrder(
            externalId: (string) $order->external_id,
            orderNumber: $order->order_number,
            status: $order->status,
            totalCents: $order->total_cents,
            currency: $order->currency,
            raw: ['manual' => true, 'order_id' => $order->id],
        );
    }

    public function registerWebhooks(Workspace $workspace): void
    {
        // No external system to register against.
    }
}
