<?php

namespace App\Support;

use App\Models\Creator;
use App\Models\CreatorNiche;
use App\Models\CreatorPortfolioItem;
use App\Models\CreatorPreference;
use App\Models\CreatorSocialAccount;
use Illuminate\Support\Str;

/**
 * Creates realistic creator rows for tests, seeders and demos without
 * touching any external service.
 */
class DemoCreatorFactory
{
    protected array $niches = [
        'Beauty & Skincare', 'Fashion', 'Food & Beverage', 'Fitness & Wellness',
        'Home & Living', 'Travel', 'Tech', 'Pets',
    ];

    protected array $names = [
        'Jamie Rivera', 'Aisha Patel', 'Morgan Chen', 'Priya Sharma', 'Sofia Garcia',
        'Leo Kim', 'Maya Nguyen', 'Noah Silva', 'Zara Ahmed', 'Eli Murphy',
        'Nina Rossi', 'Omar Khan', 'Lila Cohen', 'Kai Tanaka', 'Ivy Brooks',
    ];

    protected array $cities = [
        ['Austin', 'US'], ['London', 'GB'], ['Toronto', 'CA'], ['Mumbai', 'IN'],
        ['Brooklyn', 'US'], ['Berlin', 'DE'], ['Sydney', 'AU'], ['Miami', 'US'],
    ];

    /**
     * @return \Illuminate\Support\Collection<int, Creator>
     */
    public function create(int $count = 10)
    {
        return collect(range(1, $count))->map(fn ($i) => $this->makeOne($i));
    }

    public function makeOne(int $seed = 1): Creator
    {
        $name = $this->names[($seed - 1) % count($this->names)];
        [$city, $country] = $this->cities[($seed - 1) % count($this->cities)];
        $handle = 'creator_'.Str::slug($name).'_'.$seed;
        $creatorNiches = collect($this->niches)->random(min(2, count($this->niches)))->all();
        $followers = rand(5000, 200000);
        $engagement = round(rand(250, 900) / 100, 2);

        $creator = Creator::create([
            'uuid' => (string) Str::uuid(),
            'display_name' => $name,
            'slug' => $handle,
            'bio' => 'Creator sharing authentic takes on '.strtolower(implode(' & ', (array) $creatorNiches)).'.',
            'email' => "{$handle}@creatorflow.test",
            'country' => $country,
            'city' => $city,
            'niches' => $creatorNiches,
            'status' => 'active',
            'open_to_work' => true,
            'accepts_barter' => true,
            'accepts_paid' => true,
            'rate_ugc_cents' => rand(10000, 40000),
            'rate_video_cents' => rand(20000, 60000),
            'follower_count_total' => $followers,
            'engagement_rate' => $engagement,
            'avg_views' => (int) round($followers * ($engagement / 100)),
            'performance_score' => round(rand(65, 95), 2),
            'fraud_risk' => round(rand(2, 18), 2),
            'payout_method_status' => 'verified',
        ]);

        foreach ((array) $creatorNiches as $niche) {
            CreatorNiche::create(['creator_id' => $creator->id, 'niche' => $niche]);
        }

        CreatorSocialAccount::create([
            'creator_id' => $creator->id,
            'platform' => 'instagram',
            'handle' => $handle,
            'url' => "https://instagram.com/{$handle}",
            'follower_count' => (int) round($followers * 0.7),
            'engagement_rate' => $engagement,
        ]);

        CreatorPortfolioItem::create([
            'creator_id' => $creator->id,
            'type' => 'image',
            'title' => 'Recent UGC',
            'disk' => 'public',
            'path' => "https://picsum.photos/seed/{$handle}/600/600",
            'position' => 1,
            'metrics' => ['likes' => rand(50, 4000)],
        ]);

        CreatorPreference::create([
            'creator_id' => $creator->id,
            'barter_product_categories' => $creatorNiches,
            'shipping_address' => ['city' => $city, 'country' => $country],
            'availability_status' => 'available',
            'response_time_hours' => rand(2, 20),
        ]);

        return $creator->fresh();
    }
}
