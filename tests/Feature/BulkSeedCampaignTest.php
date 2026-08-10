<?php

namespace Tests\Feature;

use App\Domains\Campaigns\Actions\CreateCampaign;
use App\Domains\Campaigns\Actions\LaunchCampaign;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class BulkSeedCampaignTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config(['creatorflow.demo.fake_external_calls' => true]);
        Http::preventStrayRequests();
    }

    private function brand(): array
    {
        $user = User::factory()->create();
        $workspace = Workspace::create([
            'uuid' => (string) \Illuminate\Support\Str::uuid(),
            'name' => 'Test Brand',
            'plan' => 'pro',
            'plan_status' => 'active',
        ]);
        $user->workspaces()->attach($workspace->id, ['role' => 'owner', 'accepted_at' => now()]);

        return [$user, $workspace];
    }

    private function product(Workspace $ws, int $stock = 100): Product
    {
        $channel = $ws->channels()->create(['type' => 'manual', 'name' => 'Manual', 'status' => 'active']);

        $product = Product::create([
            'uuid' => (string) \Illuminate\Support\Str::uuid(),
            'workspace_id' => $ws->id,
            'channel_id' => $channel->id,
            'external_id' => 'manual_'.\Illuminate\Support\Str::uuid(),
            'title' => 'Test Serum',
            'description' => 'A great product',
            'product_type' => 'Skincare',
            'niche' => 'Beauty & Skincare',
            'hero_score' => 90,
            'status' => 'active',
        ]);

        ProductVariant::create([
            'product_id' => $product->id,
            'sku' => 'SRM-01',
            'title' => 'Default',
            'price_cents' => 2900,
            'inventory_qty' => $stock,
            'currency' => 'USD',
        ]);

        return $product;
    }

    public function test_campaign_bulk_seed_computes_invite_pool_and_products(): void
    {
        [, $workspace] = $this->brand();
        $p1 = $this->product($workspace);
        $p2 = $this->product($workspace);

        $campaign = app(CreateCampaign::class)->handle(
            $workspace,
            [
                'title' => 'Bulk seed', 'type' => 'barter', 'niche' => 'Beauty & Skincare',
                'acceptance_rate_assumed' => 25,
            ],
            [
                ['product_id' => $p1->id, 'variant_id' => $p1->variants->first()->id, 'target_creators' => 100],
                ['product_id' => $p2->id, 'variant_id' => $p2->variants->first()->id, 'target_creators' => 50],
            ]
        );

        $this->assertSame(150, $campaign->target_creators);
        // 150 / 0.25 = 600 invite pool
        $this->assertEquals(600, $campaign->invite_pool_size);
        $this->assertCount(2, $campaign->products);
        $this->assertSame(100, $campaign->products->first()->target_creators);
    }

    public function test_launch_creates_matches_and_sets_status(): void
    {
        [, $workspace] = $this->brand();
        $product = $this->product($workspace);

        // Seed some creators
        \Database\Seeders\DatabaseSeeder::class;
        $this->seedCreators();

        $campaign = app(CreateCampaign::class)->handle(
            $workspace,
            ['title' => 'Launch', 'type' => 'barter', 'niche' => 'Beauty & Skincare', 'acceptance_rate_assumed' => 50],
            [['product_id' => $product->id, 'variant_id' => $product->variants->first()->id, 'target_creators' => 3]]
        );

        app(LaunchCampaign::class)->handle($campaign, dispatchInvitations: false);

        $this->assertSame('inviting', $campaign->fresh()->status);
        $this->assertGreaterThan(0, $campaign->matches()->count());
    }

    private function seedCreators(): void
    {
        // Create a handful of creators via the seeder's creator factory logic
        $seeder = new class extends \Database\Seeders\DatabaseSeeder {
            public function make() { return $this->seedCreators(); }
        };
        // seedCreators is protected; use reflection-free path via a direct public helper:
        (new \App\Support\DemoCreatorFactory())->create(12);
    }
}
