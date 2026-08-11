<?php

namespace Database\Seeders;

use App\Domains\AI\Actions\AnalyzeStore;
use App\Domains\Campaigns\Actions\AcceptInvitation;
use App\Domains\Campaigns\Actions\CreateCampaign;
use App\Domains\Campaigns\Actions\LaunchCampaign;
use App\Domains\Content\Actions\SubmitContent;
use App\Domains\Content\Actions\ApproveContent;
use App\Domains\Matching\GenerateMatches;
use App\Domains\Analytics\Actions\AttributeOrder;
use App\Models\Attribution;
use App\Models\CampaignCreatorMatch;
use App\Models\CampaignInvitation;
use App\Models\CampaignProduct;
use App\Models\Channel;
use App\Models\ContentSubmission;
use App\Models\Creator;
use App\Models\CreatorNiche;
use App\Models\CreatorPortfolioItem;
use App\Models\CreatorPreference;
use App\Models\CreatorSocialAccount;
use App\Models\Order;
use App\Models\Payout;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use App\Models\User;
use App\Models\Workspace;
use App\Notifications\CampaignInvitation as InvitationNotification;
use Illuminate\Database\Seeder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    protected array $niches = [
        'Beauty & Skincare', 'Fashion', 'Food & Beverage', 'Fitness & Wellness',
        'Home & Living', 'Travel', 'Tech', 'Pets',
    ];

    protected array $demoProducts = [
        ['Vitamin C Serum', 'A brightening vitamin C serum for radiant skin.', 'Skincare', 2800, 320],
        ['Matcha Latte Mix', 'Ceremonial-grade matcha with oat milk powder.', 'Beverages', 2400, 500],
        ['Resistance Bands Set', '5-piece home workout resistance bands.', 'Fitness', 1900, 200],
        ['Ceramic Pour-Over Kit', 'Minimalist ceramic coffee dripper and carafe.', 'Home', 4200, 120],
        ['Hydrating Lip Balm Trio', 'Three natural lip balms in seasonal flavors.', 'Skincare', 1200, 800],
        ['Plant Protein Powder', 'Organic plant-based chocolate protein.', 'Wellness', 3900, 260],
    ];

    public function run(): void
    {
        Storage::fake('public');

        $this->command?->info('Seeding CreatorFlow demo data…');

        // SEO-focused blog posts (only if blog_posts table exists).
        $this->call(SeoBlogSeeder::class);
        // Client logos + sample reels (only if homepage_items table exists).
        $this->call(HomepageSeeder::class);

        // ── Platform superadmin (system owner) ─────────────────────────
        User::updateOrCreate(
            ['email' => 'admin@creatorflow.test'],
            [
                'uuid' => (string) Str::uuid(),
                'name' => 'Platform Admin',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'system_role' => 'superadmin',
                'account_status' => 'active',
            ]
        );

        // ── Users ──────────────────────────────────────────────────────
        $brandUser = User::updateOrCreate(
            ['email' => 'brand@creatorflow.test'],
            ['uuid' => (string) Str::uuid(), 'name' => 'Demo Brand Owner', 'password' => Hash::make('password'), 'email_verified_at' => now()]
        );

        $creatorUser = User::updateOrCreate(
            ['email' => 'creator@creatorflow.test'],
            ['uuid' => (string) Str::uuid(), 'name' => 'Jamie Rivera', 'password' => Hash::make('password'), 'email_verified_at' => now()]
        );

        // ── Workspace (Shopify merchant) ───────────────────────────────
        $workspace = Workspace::updateOrCreate(
            ['name' => 'Glow & Co.'],
            [
                'uuid' => (string) Str::uuid(),
                'agency_id' => null,
                'website' => 'https://glow-and-co.myshopify.com',
                'country' => 'US',
                'currency' => 'USD',
                'plan' => 'pro',
                'plan_status' => 'active',
                'onboarding_step' => 'complete',
                'onboarding_completed_at' => now(),
                'settings' => ['shop_domain' => 'glow-and-co.myshopify.com', 'ai_niche' => 'Beauty & Skincare'],
            ]
        );

        if (! $brandUser->workspaces()->where('workspaces.id', $workspace->id)->exists()) {
            $brandUser->workspaces()->attach($workspace->id, ['role' => 'owner', 'accepted_at' => now()]);
        }

        $shopifyChannel = Channel::updateOrCreate(
            ['workspace_id' => $workspace->id, 'type' => 'shopify', 'external_id' => 'glow-and-co.myshopify.com'],
            ['name' => 'Shopify', 'credentials' => ['access_token' => 'shpat_fake_demo_token', 'shop_domain' => 'glow-and-co.myshopify.com'], 'status' => 'active']
        );

        $manualChannel = Channel::updateOrCreate(
            ['workspace_id' => $workspace->id, 'type' => 'manual'],
            ['name' => 'Manual', 'status' => 'active']
        );

        // ── Products ───────────────────────────────────────────────────
        $this->command?->info('  → creating products');
        $products = [];
        foreach ($this->demoProducts as $i => [$title, $desc, $type, $price, $stock]) {
            $product = Product::updateOrCreate(
                ['workspace_id' => $workspace->id, 'external_id' => (string) (7000 + $i)],
                [
                    'uuid' => (string) Str::uuid(),
                    'channel_id' => $i < 4 ? $shopifyChannel->id : $manualChannel->id,
                    'title' => $title,
                    'description' => $desc,
                    'vendor' => 'Glow & Co.',
                    'product_type' => $type,
                    'niche' => in_array($type, ['Skincare', 'Wellness']) ? 'Beauty & Skincare' : 'Lifestyle',
                    'hero_score' => 95 - ($i * 6),
                    'status' => 'active',
                    'tags' => [$type, 'bestseller'],
                    'metadata' => ['shopify' => ['handle' => Str::slug($title)]],
                ]
            );

            ProductVariant::updateOrCreate(
                ['product_id' => $product->id, 'sku' => Str::upper(Str::slug($title, ''))],
                [
                    'external_id' => (string) (8000 + $i),
                    'title' => 'Default',
                    'price_cents' => $price,
                    'compare_at_cents' => $i % 2 === 0 ? (int) round($price * 1.2) : null,
                    'inventory_qty' => $stock,
                    'currency' => 'USD',
                    'weight' => 0.3,
                    'requires_shipping' => true,
                ]
            );

            ProductImage::updateOrCreate(
                ['product_id' => $product->id, 'position' => 1],
                ['disk' => 'public', 'path' => "https://picsum.photos/seed/".Str::slug($title)."/600/600", 'alt' => $title, 'is_primary' => true, 'width' => 600, 'height' => 600]
            );

            $products[] = $product->fresh('variants', 'images');
        }

        // ── AI analysis ────────────────────────────────────────────────
        $this->command?->info('  → running AI store analysis');
        try {
            app(AnalyzeStore::class)->run($workspace->fresh());
        } catch (\Throwable $e) {
            $this->command?->warn('    AI analysis skipped: '.$e->getMessage());
        }

        // ── Creators ───────────────────────────────────────────────────
        $this->command?->info('  → creating creators');
        $creators = $this->seedCreators();

        // The demo creator user maps to the first creator.
        $creators[0]->update(['user_id' => $creatorUser->id, 'display_name' => 'Jamie Rivera']);

        // ── Build a launched barter campaign with products → targets ───
        $this->command?->info('  → building campaign and bulk-seed flow');
        $campaign = app(CreateCampaign::class)->handle(
            $workspace->fresh(),
            [
                'title' => 'Glow & Co. Spring UGC Seeding',
                'type' => 'hybrid',
                'niche' => 'Beauty & Skincare',
                'summary' => 'A 6-week UGC sprint across skincare and wellness creators to generate authentic video content and drive trial.',
                'brief' => "## About Glow & Co.\nWe make clean, effective skincare.\n\n## Goal\nCreate authentic 15-45s UGC showing real results.\n\n## Requirements\n- Show product within first 3 seconds\n- Mention the key benefit naturally\n- Use your unique discount code\n- Tag #gifted #ad",
                'objectives' => ['ugc', 'awareness', 'sales'],
                'content_types' => ['video', 'story'],
                'creator_fee_cents' => 5000,
                'acceptance_rate_assumed' => 30,
                'ai_generated' => true,
                'ai_predicted_roi' => 2.4,
            ],
            [
                ['product_id' => $products[0]->id, 'variant_id' => $products[0]->variants->first()->id, 'target_creators' => 8, 'fee_cents' => 5000],
                ['product_id' => $products[1]->id, 'variant_id' => $products[1]->variants->first()->id, 'target_creators' => 5, 'fee_cents' => 5000],
                ['product_id' => $products[2]->id, 'variant_id' => $products[2]->variants->first()->id, 'target_creators' => 4, 'fee_cents' => 3000],
            ],
            $brandUser->id,
        );

        // ── AI matches ────────────────────────────────────────────────
        try {
            app(GenerateMatches::class)->run($campaign, limit: 40);
        } catch (\Throwable $e) {
            $this->command?->warn('    matching skipped: '.$e->getMessage());
        }

        // ── Launch (dispatches invitations) ───────────────────────────
        try {
            app(LaunchCampaign::class)->handle($campaign, dispatchInvitations: false);
        } catch (\Throwable $e) {
            $this->command?->warn('    launch skipped: '.$e->getMessage());
        }

        // ── Manually create invitations from top matches (synchronous) ─
        $topMatches = $campaign->matches()->orderByDesc('score')->take(17)->get();
        $accept = app(AcceptInvitation::class);
        $attribute = app(AttributeOrder::class);
        $acceptedSoFar = 0;
        $targetAccepted = 17;

        foreach ($topMatches as $index => $match) {
            $cp = $campaign->products->get($index % $campaign->products->count());

            $invitation = CampaignInvitation::create([
                'uuid' => (string) Str::uuid(),
                'campaign_id' => $campaign->id,
                'creator_id' => $match->creator_id,
                'campaign_product_id' => $cp->id,
                'channel' => 'in_app',
                'message' => "Hey {$match->creator->display_name}, your content is a perfect fit for {$cp->product->title}!",
                'ai_variant' => 'default',
                'status' => 'sent',
                'sent_at' => now()->subDays(10 - $index),
                'expires_at' => now()->addDays(7),
            ]);

            $match->update(['status' => 'invited', 'invited_at' => now()->subDays(10 - $index)]);

            try {
                $match->creator->notify(new InvitationNotification($campaign->id, $match->creator_id));
            } catch (\Throwable) {}

            // Accept roughly the first 17 to hit target.
            if ($acceptedSoFar < $targetAccepted) {
                try {
                    $assignment = $accept->handle($invitation);
                    // Run order creation synchronously so the demo has orders
                    // immediately (production dispatches it to the queue).
                    if (! $assignment->channel_order_id) {
                        (new \App\Jobs\CreateCreatorOrder($assignment->id))
                            ->handle(app(\App\Domains\Commerce\Channels\ChannelRegistry::class));
                        $assignment->refresh();
                    }
                    $this->progressAssignment($assignment->fresh(), $index, $campaign, $attribute, $products);
                    $acceptedSoFar++;
                } catch (\Throwable $e) {
                    $this->command?->warn('    assignment failed: '.$e->getMessage());
                }
            }
        }

        // Also seed a second completed campaign for analytics.
        $this->seedHistoricalCampaign($workspace, $products[4], $creators, $brandUser, $attribute);

        $this->command?->info('✓ Done. Login as brand@creatorflow.test / password or creator@creatorflow.test / password');
    }

    /**
     * Walk an assignment through its lifecycle based on index so the demo
     * shows a realistic spread of statuses, content, orders and payouts.
     */
 protected function progressAssignment($assignment, int $index, $campaign, AttributeOrder $attribute, $products): void
    {
        $cp = $assignment->campaignProduct;
        $creator = $assignment->creator;

        // Price used for the simulated attributed order below.
        $price = $cp->variant?->price_cents
            ?? $cp->product->variants->first()?->price_cents
            ?? $cp->product->priceCents();

        // Sign contract
        $assignment->contract?->signAsCreator('127.0.0.1');
        $assignment->update(['status' => 'contract_signed']);
        // The order was already created by CreateCreatorOrder (run synchronously
        // in run()). Fetch it and advance its lifecycle.
        $order = $assignment->order;
        if ($order) {
            $order->update([
                'status' => 'fulfilled',
                'tracking_number' => '1Z'.Str::upper(Str::random(12)),
                'fulfilled_at' => now()->subDays(6),
                'delivered_at' => now()->subDays(4),
            ]);
            $assignment->update(['status' => 'delivered']);
        }

        if ($index >= 4) {
            // Submitted content
            $assignment->update(['status' => 'in_progress']);
            $submission = $this->createSubmission($assignment, $index);
            $assignment->update(['status' => 'submitted']);

            // Mark AI reviewed
            $submission->update([
                'ai_score' => 80 + ($index % 15),
                'ai_feedback' => ['Product shown early', 'Brand mention present'],
                'status' => 'in_review',
            ]);
        }

        if ($index >= 10) {
            // Approved + payout + attributed sale
            $sub = $assignment->submissions()->first();
            if ($sub) {
                app(ApproveContent::class)->handle($sub);
            }
            $assignment->update(['status' => 'completed']);

            // Simulate a customer order attributed to this creator's code.
            $attributedOrder = Order::create([
                'uuid' => (string) Str::uuid(),
                'workspace_id' => $campaign->workspace_id,
                'channel_id' => $cp->product->channel_id,
                'creator_id' => $creator->id,
                'external_id' => 'sale_'.Str::uuid(),
                'order_number' => '#'.rand(10000, 99999),
                'total_cents' => $price,
                'subtotal_cents' => $price,
                'currency' => 'USD',
                'status' => 'paid',
                'placed_at' => now()->subDays(rand(0, 3)),
                'raw_payload' => ['discount_codes' => [['code' => $assignment->discount_code]]],
            ]);
            $attribute->handle($attributedOrder);
        }
    }

    protected function createSubmission($assignment, int $index): ContentSubmission
    {
        // Use a tiny 1x1 transparent PNG so we don't need real media files.
        $png = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNk+M8AAAMBAQDJ/pLvAAAAAElFTkSuQmCC');
        $path = 'ugc/seed-'.$assignment->id.'-'.$index.'.png';
        Storage::disk('public')->put($path, $png);

        return ContentSubmission::create([
            'uuid' => (string) Str::uuid(),
            'assignment_id' => $assignment->id,
            'creator_id' => $assignment->creator_id,
            'campaign_id' => $assignment->campaign_id,
            'type' => $index % 3 === 0 ? 'image' : 'video',
            'disk' => 'public',
            'path' => $path,
            'caption' => 'Loving this product! ##gifted',
            'status' => 'submitted',
            'submitted_at' => now()->subDays(3),
        ]);
    }

    protected function seedHistoricalCampaign(Workspace $workspace, Product $product, array $creators, User $brandUser, AttributeOrder $attribute): void
    {
        $campaign = app(CreateCampaign::class)->handle(
            $workspace,
            [
                'title' => 'Holiday Lip Balm Seeding (completed)',
                'type' => 'barter', 'niche' => 'Beauty & Skincare',
                'summary' => 'Past campaign for analytics.',
                'brief' => 'Show the lip balm trio in your daily routine.',
                'acceptance_rate_assumed' => 40,
                'status' => 'completed',
            ],
            [['product_id' => $product->id, 'variant_id' => $product->variants[0]->id, 'target_creators' => 5]],
            $brandUser->id,
        );
        $campaign->update(['status' => 'completed', 'launched_at' => now()->subDays(40), 'completed_at' => now()->subDays(5)]);

        foreach (array_slice($creators, 5, 5) as $i => $creator) {
            $cp = $campaign->products->first();
            CampaignCreatorMatch::create(['campaign_id' => $campaign->id, 'creator_id' => $creator->id, 'score' => 80 - $i * 3, 'reasons' => ['Past performer'], 'status' => 'accepted']);

            $assignment = \App\Models\CampaignAssignment::create([
                'uuid' => (string) Str::uuid(),
                'campaign_id' => $campaign->id,
                'campaign_product_id' => $cp->id,
                'creator_id' => $creator->id,
                'status' => 'completed',
                'fee_cents' => 0,
            ]);

            $price = $product->priceCents();
            for ($s = 0; $s < rand(1, 3); $s++) {
                $order = Order::create([
                    'uuid' => (string) Str::uuid(),
                    'workspace_id' => $workspace->id,
                    'channel_id' => $product->channel_id,
                    'creator_id' => $creator->id,
                    'external_id' => 'hist_'.Str::uuid(),
                    'order_number' => '#'.rand(20000, 99999),
                    'total_cents' => $price, 'subtotal_cents' => $price,
                    'currency' => 'USD', 'status' => 'paid',
                    'placed_at' => now()->subDays(rand(10, 35)),
                    'raw_payload' => ['discount_codes' => [['code' => 'HIST'.Str::upper(Str::random(6))]]],
                ]);
                Attribution::create([
                    'workspace_id' => $workspace->id,
                    'order_id' => $order->id,
                    'creator_id' => $creator->id,
                    'campaign_id' => $campaign->id,
                    'assignment_id' => $assignment->id,
                    'model' => 'discount_code',
                    'weight' => 1,
                    'revenue_cents' => $price,
                    'currency' => 'USD',
                    'attributed_at' => now()->subDays(rand(10, 35)),
                    'created_at' => now()->subDays(rand(10, 35)),
                ]);
            }
        }
    }

    protected function seedCreators(): array
    {
        $firstNames = ['Jamie', 'Aisha', 'Morgan', 'Priya', 'Sofia', 'Leo', 'Maya', 'Noah', 'Zara', 'Eli', 'Nina', 'Omar', 'Lila', 'Kai', 'Ivy', 'Theo', 'Ruby', 'Sam', 'Ava', 'Milo'];
        $handles = ['glowwithjamie', 'aishaeats', 'morganmoves', 'priyapours', 'sofiastyles', 'leolifts', 'mayamakes', 'noahknows', 'zarazone', 'elieats', 'ninanotes', 'omaroutdoors', 'lilalooks', 'kaikneads', 'ivyinvests', 'theotrends', 'rubyruns', 'samskincare', 'avaathome', 'milomixes'];
        $cities = [['Austin', 'US'], ['London', 'GB'], ['Toronto', 'CA'], ['Mumbai', 'IN'], ['Brooklyn', 'US'], ['Berlin', 'DE'], ['Sydney', 'AU'], ['Austin', 'US'], ['Miami', 'US'], ['Nashville', 'US']];

        $creators = [];
        foreach ($firstNames as $i => $name) {
            [$city, $country] = $cities[$i % count($cities)];
            $creatorNiches = collect($this->niches)->random(rand(1, 3))->all();
            $followers = rand(3000, 250000);
            $engagement = round(rand(250, 950) / 100, 2);

            $creator = Creator::updateOrCreate(
                ['slug' => Str::slug($handles[$i])],
                [
                    'uuid' => (string) Str::uuid(),
                    'display_name' => $name.' '.['Lopez','Patel','Chen','Garcia','Kim','Nguyen','Silva','Ahmed','Murphy','Ross'][$i % 10],
                    'bio' => 'Creator sharing authentic takes on '.strtolower(implode(' & ', $creatorNiches)).'.',
                    'email' => Str::slug($handles[$i]).'@creatorflow.test',
                    'country' => $country,
                    'city' => $city,
                    'niches' => $creatorNiches,
                    'status' => 'active',
                    'open_to_work' => true,
                    'accepts_barter' => true,
                    'accepts_paid' => true,
                    'rate_ugc_cents' => rand(10000, 50000),
                    'rate_video_cents' => rand(15000, 75000),
                    'rate_post_cents' => rand(8000, 40000),
                    'follower_count_total' => $followers,
                    'engagement_rate' => $engagement,
                    'avg_views' => (int) round($followers * ($engagement / 100)),
                    'performance_score' => round(rand(60, 98), 2),
                    'fraud_risk' => round(rand(2, 20), 2),
                    'payout_method_status' => 'verified',
                    'ai_summary' => 'Strong UGC creator with reliable delivery.',
                ]
            );

            CreatorNiche::where('creator_id', $creator->id)->delete();
            foreach ($creatorNiches as $n) {
                CreatorNiche::create(['creator_id' => $creator->id, 'niche' => $n]);
            }

            CreatorSocialAccount::updateOrCreate(
                ['creator_id' => $creator->id, 'platform' => 'instagram'],
                ['handle' => $handles[$i], 'url' => 'https://instagram.com/'.$handles[$i], 'follower_count' => (int) round($followers * 0.7), 'engagement_rate' => $engagement, 'verified' => $i % 5 === 0]
            );
            CreatorSocialAccount::updateOrCreate(
                ['creator_id' => $creator->id, 'platform' => 'tiktok'],
                ['handle' => $handles[$i], 'url' => 'https://tiktok.com/@'.$handles[$i], 'follower_count' => (int) round($followers * 0.5), 'engagement_rate' => $engagement + 1]
            );

            CreatorPortfolioItem::updateOrCreate(
                ['creator_id' => $creator->id, 'position' => 1],
                ['type' => 'image', 'title' => 'Recent UGC', 'disk' => 'public', 'path' => "https://picsum.photos/seed/{$handles[$i]}/600/600", 'metrics' => ['likes' => rand(100, 5000)]]
            );

            CreatorPreference::updateOrCreate(
                ['creator_id' => $creator->id],
                ['barter_product_categories' => $creatorNiches, 'shipping_address' => ['city' => $city, 'country' => $country], 'availability_status' => 'available', 'response_time_hours' => rand(2, 24)]
            );

            $creators[] = $creator->fresh();
        }

        return $creators;
    }
}
