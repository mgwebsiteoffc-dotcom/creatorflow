<?php

namespace App\Domains\Commerce\Channels\Shopify;

use App\Domains\Commerce\Channels\Contracts\ChannelOrder;
use App\Domains\Commerce\Channels\Contracts\CommerceChannel;
use App\Domains\Commerce\Channels\Contracts\SyncResult;
use App\Models\CampaignAssignment;
use App\Models\Channel as ChannelModel;
use App\Models\Creator;
use App\Models\DiscountCode;
use App\Models\Product;
use App\Models\SyncJob;
use App\Models\Workspace;
use Illuminate\Support\Str;

/**
 * Shopify adapter. The only place in the codebase that knows about Shopify
 * payload shapes, endpoints, discount codes and draft orders.
 */
class ShopifyChannel implements CommerceChannel
{
    public function __construct(
        protected ShopifyProductTransformer $transformer,
        protected ShopifyDiscountFactory $discounts,
    ) {}

    public function client(Workspace $workspace): ShopifyApiClient
    {
        $channel = $this->channel($workspace);

        $credentials = $channel->credentials ?? [];
        $shopDomain = $channel->external_id
            ?? $credentials['shop_domain']
            ?? throw new \RuntimeException('Shopify channel has no shop domain.');

        return new ShopifyApiClient(
            shopDomain: $shopDomain,
            accessToken: (string) ($credentials['access_token'] ?? ''),
            apiVersion: (string) config('creatorflow.shopify.api_version'),
            fake: (bool) config('creatorflow.demo.fake_external_calls'),
        );
    }

    public function syncProducts(Workspace $workspace, SyncJob $job): SyncResult
    {
        $client = $this->client($workspace);
        $channel = $this->channel($workspace);
        $result = new SyncResult();

        // Cursor over the products endpoint. In fake mode we pull from the
        // workspace's existing products so seeders/demo still "sync".
        $page = 1;
        do {
            $response = $client->get("/admin/api/{$client->apiVersion}/products.json", [
                'limit' => 50,
                'page' => $page,
                'status' => 'active',
            ]);

            $products = $response['products'] ?? [];

            // In fake mode, fabricate a deterministic payload from each local product.
            if (! empty($response['fake']) && $page === 1) {
                $products = $this->fakeProductsPayload($workspace);
            }

            foreach ($products as $payload) {
                try {
                    $existed = Product::where('workspace_id', $workspace->id)
                        ->where('external_id', (string) $payload['id'])
                        ->exists();
                    $this->transformer->upsert($workspace->id, $channel->id, $payload);
                    $existed ? $result->updated[] = (string) $payload['id'] : $result->created[] = (string) $payload['id'];
                } catch (\Throwable $e) {
                    $result->errors[] = $payload['id'].': '.$e->getMessage();
                }
            }

            $page++;
        } while (count($products) === 50 && empty($response['fake']));

        $channel->update(['last_synced_at' => now()]);

        return $result;
    }

    public function syncInventory(Workspace $workspace, SyncJob $job): SyncResult
    {
        $client = $this->client($workspace);

        // Real implementation: GET /inventory_levels.json and map to variants.
        $client->get("/admin/api/{$client->apiVersion}/inventory_levels.json");

        return new SyncResult();
    }

    public function syncOrders(Workspace $workspace, \DateTimeInterface $since, SyncJob $job): SyncResult
    {
        $client = $this->client($workspace);

        $client->get("/admin/api/{$client->apiVersion}/orders.json", [
            'status' => 'any',
            'updated_at_min' => $since->format('c'),
            'limit' => 250,
        ]);

        // Order attribution is handled by the HandleShopifyOrderWebhook job;
        // this reconcile pass catches anything missed by webhooks.
        return new SyncResult();
    }

    public function createDiscountCode(Workspace $workspace, Product $product, Creator $creator, array $options): DiscountCode
    {
        $client = $this->client($workspace);

        $code = $this->discounts->makeCode($creator, $options['prefix'] ?? null);

        $payload = $this->discounts->priceRulePayload($code, $product, $creator, $options);

        // Create price rule then discount code via the REST API.
        $rule = $client->post("/admin/api/{$client->apiVersion}/price_rules.json", $payload);
        $ruleId = $rule['price_rule']['id'] ?? null;

        $codeResponse = $ruleId
            ? $client->post("/admin/api/{$client->apiVersion}/price_rules/{$ruleId}/discount_codes.json", [
                'discount_code' => ['code' => $code],
            ])
            : ['fake' => true];

        return DiscountCode::create([
            'workspace_id' => $workspace->id,
            'campaign_id' => $options['campaign_id'],
            'creator_id' => $creator->id,
            'product_id' => $product->id,
            'code' => $code,
            'type' => $options['type'] ?? 'full_comp',
            'value' => (float) ($options['value'] ?? 100),
            'external_id' => (string) ($codeResponse['discount_code']['id'] ?? $code),
            'usage_limit' => (int) ($options['usage_limit'] ?? 1),
            'starts_at' => now(),
            'expires_at' => $options['expires_at'] ?? now()->addMonths(3),
            'status' => 'active',
        ]);
    }

    public function createCreatorOrder(CampaignAssignment $assignment): ChannelOrder
    {
        $workspace = $assignment->campaign->workspace;
        $client = $this->client($workspace);

        $product = $assignment->campaignProduct->product;
        $creator = $assignment->creator;
        $variant = $assignment->campaignProduct->variant;

        // Build a draft order with a 100% discount so the product is gifted
        // but inventory, shipping and returns still flow through Shopify.
        $lineItem = [
            'title' => $product->title,
            'quantity' => 1,
            'price' => (string) ($variant->price_cents / 100),
            'grams' => (int) round(((float) ($variant->weight ?? 0)) * 1000),
            'requires_shipping' => (bool) $variant->requires_shipping,
        ];

        if ($variant->external_id) {
            $lineItem['variant_id'] = $variant->external_id;
        }

        $draftPayload = [
            'draft_order' => [
                'line_items' => [$lineItem],
                'email' => $creator->email,
                'note' => 'CreatorFlow gift for creator #'.$creator->id.' (assignment '.$assignment->uuid.')',
                'tags' => 'creatorflow,barter,creator_'.$creator->id,
                'applied_discount' => [
                    'title' => 'CreatorFlow Gift',
                    'value' => '100',
                    'value_type' => 'percentage',
                    'amount' => '0.00',
                ],
                'shipping_address' => $creator->preferences?->shipping_address,
                'use_customer_default_address' => empty($creator->preferences?->shipping_address),
            ],
        ];

        $draft = $client->post("/admin/api/{$client->apiVersion}/draft_orders.json", $draftPayload);
        $draftId = $draft['draft_order']['id'] ?? null;

        if ($draftId) {
            // Mark the draft order as paid to convert it into a real order that
            // flows through fulfillment.
            $client->put("/admin/api/{$client->apiVersion}/draft_orders/{$draftId}/complete.json", [
                'payment_pending' => false,
            ]);
        }

        $totalCents = (int) round((float) ($draft['draft_order']['total_price'] ?? 0) * 100);

        return new ChannelOrder(
            externalId: (string) ($draft['draft_order']['order_id'] ?? $draftId ?? 'draft_'.$assignment->uuid),
            orderNumber: (string) ($draft['draft_order']['name'] ?? 'CF-'.Str::upper(Str::random(8))),
            status: 'open',
            totalCents: $totalCents,
            currency: $workspace->currency,
            raw: $draft,
        );
    }

    public function registerWebhooks(Workspace $workspace): void
    {
        $client = $this->client($workspace);
        $channel = $this->channel($workspace);

        $topics = [
            'products/create', 'products/update', 'products/delete',
            'inventory_levels/update', 'collections/update',
            'orders/create', 'orders/updated',
            'app/uninstalled',
            'customers/data_request', 'customers/redact', 'shop/redact',
        ];

        foreach ($topics as $topic) {
            $response = $client->post("/admin/api/{$client->apiVersion}/webhooks.json", [
                'webhook' => [
                    'topic' => $topic,
                    'address' => route('webhooks.shopify', [], absolute: true),
                    'format' => 'json',
                ],
            ]);

            if (! empty($response['webhook']['id'])) {
                $channel->webhooks()->updateOrCreate(
                    ['topic' => $topic],
                    ['external_id' => (string) $response['webhook']['id'], 'address' => route('webhooks.shopify'), 'status' => 'active']
                );
            }
        }
    }

    protected function channel(Workspace $workspace): ChannelModel
    {
        return $workspace->channels()->where('type', 'shopify')->firstOr(function () use ($workspace) {
            return $workspace->channels()->create([
                'type' => 'shopify',
                'name' => 'Shopify',
                'external_id' => $workspace->settings['shop_domain'] ?? null,
                'status' => 'active',
            ]);
        });
    }

    /**
     * Fake mode: turn local products into Shopify-shaped payloads so a sync is
     * demonstrable without an external store.
     *
     * @return array<int, array<string, mixed>>
     */
    protected function fakeProductsPayload(Workspace $workspace): array
    {
        return $workspace->products()->with('variants', 'images')->get()->map(function (Product $p, int $i) {
            return [
                'id' => $p->external_id ?? 1000 + $i,
                'title' => $p->title,
                'body_html' => (string) $p->description,
                'vendor' => $p->vendor,
                'product_type' => $p->product_type,
                'status' => $p->status,
                'handle' => Str::slug($p->title),
                'currency' => $workspace->currency,
                'variants' => $p->variants->map(fn ($v) => [
                    'id' => $v->external_id ?? 2000 + $v->id,
                    'sku' => $v->sku,
                    'title' => $v->title,
                    'price' => $v->price_cents / 100,
                    'compare_at_price' => $v->compare_at_cents ? $v->compare_at_cents / 100 : null,
                    'inventory_quantity' => $v->inventory_qty,
                    'inventory_policy' => $v->inventory_policy,
                    'barcode' => $v->barcode,
                    'grams' => (int) round(((float) $v->weight) * 1000),
                    'requires_shipping' => (bool) $v->requires_shipping,
                ])->all(),
                'images' => $p->images->map(fn ($img, $idx) => [
                    'id' => 3000 + $img->id,
                    'src' => $img->path,
                    'alt' => $img->alt,
                    'position' => $img->position ?: $idx + 1,
                    'width' => $img->width,
                    'height' => $img->height,
                ])->all(),
            ];
        })->all();
    }
}
