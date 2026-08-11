<?php

namespace Tests\Feature;

use App\Jobs\HandleShopifyWebhook;
use App\Models\Channel;
use App\Models\Product;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Tests\TestCase;

class ShopifyWebhookTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config(['creatorplex.demo.fake_external_calls' => true]);
    }

    private function shopifyChannel(): Channel
    {
        $user = User::factory()->create();
        $ws = Workspace::create([
            'uuid' => (string) \Illuminate\Support\Str::uuid(),
            'name' => 'Shop', 'plan' => 'pro', 'plan_status' => 'active',
            'settings' => ['shop_domain' => 'shop.myshopify.com'],
        ]);
        $user->workspaces()->attach($ws->id, ['role' => 'owner', 'accepted_at' => now()]);

        return Channel::create([
            'workspace_id' => $ws->id,
            'type' => 'shopify',
            'name' => 'Shopify',
            'external_id' => 'shop.myshopify.com',
            'credentials' => ['access_token' => 'fake', 'shop_domain' => 'shop.myshopify.com'],
            'status' => 'active',
        ]);
    }

    public function test_valid_webhook_dispatches_job(): void
    {
        Bus::fake();
        $channel = $this->shopifyChannel();

        $response = $this->postJson('/webhooks/shopify', [
            'id' => 123, 'title' => 'From Shopify',
        ], [
            'X-Shopify-Topic' => 'products/create',
            'X-Shopify-Shop-Domain' => 'shop.myshopify.com',
            'X-Shopify-Webhook-Id' => 'wh_1',
        ]);

        $response->assertStatus(200);
        Bus::assertDispatched(HandleShopifyWebhook::class, function ($job) use ($channel) {
            return $job->workspaceId === $channel->workspace_id
                && $job->topic === 'products/create';
        });
    }

    public function test_product_webhook_upserts_product(): void
    {
        $channel = $this->shopifyChannel();

        $payload = [
            'id' => 998877,
            'title' => 'Shopify Synced Serum',
            'body_html' => '<p>Synced from Shopify</p>',
            'vendor' => 'Glow & Co.',
            'product_type' => 'Skincare',
            'status' => 'active',
            'variants' => [[
                'id' => 5566, 'sku' => 'SSS-1', 'title' => 'Default',
                'price' => '34.00', 'inventory_quantity' => 75, 'requires_shipping' => true,
            ]],
            'images' => [[
                'id' => 11, 'src' => 'https://example.com/p.png', 'position' => 1,
                'width' => 600, 'height' => 600,
            ]],
        ];

        (new HandleShopifyWebhook($channel->workspace_id, 'products/create', $payload, 'wh_2'))->handle(
            app(\App\Domains\Commerce\Channels\Shopify\ShopifyProductTransformer::class),
            app(\App\Domains\Analytics\Actions\AttributeOrder::class),
        );

        $this->assertDatabaseHas('products', [
            'external_id' => '998877',
            'title' => 'Shopify Synced Serum',
            'channel_id' => $channel->id,
        ]);

        $product = Product::where('external_id', '998877')->first();
        $this->assertSame(3400, $product->variants->first()->price_cents);
        $this->assertSame(75, $product->variants->first()->inventory_qty);
    }
}
