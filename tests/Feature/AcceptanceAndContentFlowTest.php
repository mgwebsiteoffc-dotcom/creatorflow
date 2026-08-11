<?php

namespace Tests\Feature;

use App\Domains\Campaigns\Actions\AcceptInvitation;
use App\Domains\Campaigns\Actions\CreateCampaign;
use App\Domains\Content\Actions\ApproveContent;
use App\Domains\Content\Actions\SubmitContent;
use App\Domains\Analytics\Actions\AttributeOrder;
use App\Models\CampaignInvitation;
use App\Models\ContentSubmission;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use App\Models\Workspace;
use App\Support\DemoCreatorFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AcceptanceAndContentFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config(['creatorplex.demo.fake_external_calls' => true]);
        Http::preventStrayRequests();
        Storage::fake('public');
    }

    private function workspaceWithProduct(): array
    {
        $user = User::factory()->create();
        $ws = Workspace::create([
            'uuid' => (string) \Illuminate\Support\Str::uuid(),
            'name' => 'Brand', 'plan' => 'pro', 'plan_status' => 'active',
        ]);
        $user->workspaces()->attach($ws->id, ['role' => 'owner', 'accepted_at' => now()]);

        $channel = $ws->channels()->create(['type' => 'manual', 'name' => 'Manual', 'status' => 'active']);

        $product = Product::create([
            'uuid' => (string) \Illuminate\Support\Str::uuid(),
            'workspace_id' => $ws->id,
            'channel_id' => $channel->id,
            'external_id' => 'manual_'.\Illuminate\Support\Str::uuid(),
            'title' => 'Serum', 'product_type' => 'Skincare',
            'niche' => 'Beauty & Skincare', 'hero_score' => 90, 'status' => 'active',
        ]);
        ProductVariant::create([
            'product_id' => $product->id, 'sku' => 'SRM',
            'title' => 'Default', 'price_cents' => 2900,
            'inventory_qty' => 50, 'currency' => 'USD',
        ]);

        return [$user, $ws->fresh(), $product->fresh()];
    }

    private function invitation($ws, $product, $creator): CampaignInvitation
    {
        $campaign = app(CreateCampaign::class)->handle(
            $ws,
            ['title' => 'Seeding', 'type' => 'barter', 'niche' => 'Beauty & Skincare', 'acceptance_rate_assumed' => 30],
            [['product_id' => $product->id, 'variant_id' => $product->variants->first()->id, 'target_creators' => 5]]
        );

        return CampaignInvitation::create([
            'uuid' => (string) \Illuminate\Support\Str::uuid(),
            'campaign_id' => $campaign->id,
            'creator_id' => $creator->id,
            'campaign_product_id' => $campaign->products->first()->id,
            'channel' => 'in_app', 'status' => 'sent', 'sent_at' => now(),
            'expires_at' => now()->addDays(7),
        ]);
    }

    public function test_accepting_invitation_creates_contract_discount_and_order(): void
    {
        [, $ws, $product] = $this->workspaceWithProduct();
        $creator = (new DemoCreatorFactory())->makeOne(1);
        $invitation = $this->invitation($ws, $product, $creator);

        $assignment = app(AcceptInvitation::class)->handle($invitation);

        $this->assertDatabaseHas('campaign_assignments', ['id' => $assignment->id, 'status' => 'contract_sent']);
        $this->assertNotNull($assignment->discount_code);
        $this->assertDatabaseHas('contracts', ['creator_id' => $creator->id]);
        $this->assertDatabaseHas('discount_codes', ['creator_id' => $creator->id, 'code' => $assignment->discount_code]);

        // Order is created via a queued job; with QUEUE_CONNECTION=sync it runs inline.
        $this->assertDatabaseHas('orders', ['creator_id' => $creator->id, 'workspace_id' => $ws->id]);
    }

    public function test_content_submission_approval_releases_payout(): void
    {
        [, $ws, $product] = $this->workspaceWithProduct();
        $creator = (new DemoCreatorFactory())->makeOne(2);
        $invitation = $this->invitation($ws, $product, $creator);

        $assignment = app(AcceptInvitation::class)->handle($invitation);
        $assignment->update(['fee_cents' => 10000]); // paid campaign

        $file = UploadedFile::fake()->image('ugc.png');
        $submission = app(SubmitContent::class)->handle($assignment, 'image', $file, ['caption' => 'Loving it']);

        $this->assertDatabaseHas('content_submissions', ['id' => $submission->id]);

        // Manually approve (AI review runs on queue)
        app(ApproveContent::class)->handle($submission);

        $this->assertDatabaseHas('payouts', [
            'creator_id' => $creator->id,
            'amount_cents' => 10000,
            'status' => 'paid', // fake Stripe driver marks paid immediately
        ]);
        $this->assertSame('completed', $assignment->fresh()->status);
    }

    public function test_discount_code_attribution(): void
    {
        [, $ws, $product] = $this->workspaceWithProduct();
        $creator = (new DemoCreatorFactory())->makeOne(3);
        $invitation = $this->invitation($ws, $product, $creator);

        $assignment = app(AcceptInvitation::class)->handle($invitation);

        $order = Order::create([
            'uuid' => (string) \Illuminate\Support\Str::uuid(),
            'workspace_id' => $ws->id,
            'channel_id' => $product->channel_id,
            'creator_id' => $creator->id,
            'external_id' => 'sale_'.\Illuminate\Support\Str::uuid(),
            'order_number' => '#12345',
            'total_cents' => 2900,
            'subtotal_cents' => 2900,
            'currency' => 'USD',
            'status' => 'paid',
            'raw_payload' => ['discount_codes' => [['code' => $assignment->discount_code]]],
        ]);

        $attribution = app(AttributeOrder::class)->handle($order);

        $this->assertNotNull($attribution);
        $this->assertSame($creator->id, $attribution->creator_id);
        $this->assertSame(2900, $attribution->revenue_cents);
        $this->assertSame('discount_code', $attribution->model);
    }
}
