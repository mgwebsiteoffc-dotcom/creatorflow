<?php

namespace App\Domains\Commerce\Channels\Contracts;

use App\Models\Creator;
use App\Models\Product;
use App\Models\CampaignAssignment;
use App\Models\DiscountCode;
use App\Models\SyncJob;
use App\Models\Workspace;

/**
 * The single integration contract every sales channel implements.
 *
 * Shopify is the richest adapter; WooCommerce, Amazon, CSV and the manual
 * channel implement the same interface. Domain services depend ONLY on this
 * abstraction, so campaign/order logic never branches on channel type.
 */
interface CommerceChannel
{
    /**
     * Pull products (and variants/images/collections) from the remote store.
     */
    public function syncProducts(Workspace $workspace, SyncJob $job): SyncResult;

    /**
     * Pull current inventory levels.
     */
    public function syncInventory(Workspace $workspace, SyncJob $job): SyncResult;

    /**
     * Pull orders updated since the given time (used for attribution).
     */
    public function syncOrders(Workspace $workspace, \DateTimeInterface $since, SyncJob $job): SyncResult;

    /**
     * Create a unique discount code for a creator within a campaign.
     */
    public function createDiscountCode(Workspace $workspace, Product $product, Creator $creator, array $options): DiscountCode;

    /**
     * Create a fulfillment order for a creator who accepted an assignment.
     *
     * For Shopify this is a Draft Order converted to a $0/tagged paid order so
     * inventory, shipping and returns stay inside the merchant's store. Other
     * channels may return an "external reference" order or a manual record.
     */
    public function createCreatorOrder(CampaignAssignment $assignment): ChannelOrder;

    /**
     * Register the webhooks required for incremental sync.
     */
    public function registerWebhooks(Workspace $workspace): void;
}
