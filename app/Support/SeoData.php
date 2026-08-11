<?php

namespace App\Support;

/**
 * Programmatic SEO source data.
 *
 * We generate 60+ landing pages from the cartesian product of:
 *   • services (11 keyword-anchored services)
 *   • cities   (10 top Indian metros + "india")
 *
 * Every combination becomes /services/{service}/{city} with unique H1,
 * meta, FAQs, and schema. Each service also has a national page at
 * /services/{service} (no city).
 */
class SeoData
{
    /**
     * Every service = one keyword bucket we want to rank for.
     * Keys become URL slugs.
     */
    public static function services(): array
    {
        return [
            'influencer-marketing-agency' => [
                'name'      => 'Influencer Marketing Agency',
                'short'     => 'influencer marketing',
                'tagline'   => 'End-to-end influencer marketing agency for DTC brands.',
                'emoji'     => '📣',
                'grad'      => 'from-violet-500 to-pink-500',
                'accent'    => '#a855f7',
                'intent'    => 'You want an agency to run creator campaigns for you — matching, briefs, contracts, content review, attribution.',
                'good_for'  => ['DTC brands scaling to ₹1Cr+/mo', 'Shopify stores wanting hands-off ops', 'Founders who want ROI, not spreadsheets'],
                'kpi'       => [['1,000+', 'Brands served'], ['100K+', 'Verified creators'], ['5.4×', 'Avg ROAS']],
                'related_service_slugs' => ['ugc-influencers','barter-influencers','creator-marketplace','paid-influencer-campaigns'],
            ],
            'ugc-influencers' => [
                'name'      => 'UGC Influencers',
                'short'     => 'UGC influencer marketing',
                'tagline'   => 'Studio-quality UGC from verified creators — ready for paid ads.',
                'emoji'     => '🎬',
                'grad'      => 'from-emerald-500 to-teal-500',
                'accent'    => '#10b981',
                'intent'    => 'You need ad-ready UGC video from real creators — for Meta, TikTok, YouTube.',
                'good_for'  => ['Performance marketers needing fresh creative', 'DTC brands scaling paid ads', 'Agencies building UGC libraries'],
                'kpi'       => [['9:16 · 1:1 · 16:9', 'Formats'], ['Full', 'Usage rights'], ['14 days', 'Turnaround']],
                'related_service_slugs' => ['influencer-marketing-agency','barter-influencers','paid-influencer-campaigns','creator-videoshoot'],
            ],
            'barter-influencers' => [
                'name'      => 'Barter Influencers',
                'short'     => 'barter influencer campaigns',
                'tagline'   => 'Trade product for content — zero cash, 62% average acceptance rate.',
                'emoji'     => '🎁',
                'grad'      => 'from-pink-500 to-rose-500',
                'accent'    => '#ec4899',
                'intent'    => 'You want to seed 100s of creators with product only — no creator fees.',
                'good_for'  => ['Beauty, fashion, food & lifestyle brands', 'Launches that need volume UGC', 'DTC teams with tight cash flow'],
                'kpi'       => [['62%', 'Accept rate'], ['4–8×', 'ROAS on retail'], ['Auto', 'Shopify orders']],
                'related_service_slugs' => ['ugc-influencers','micro-influencer-marketing','shopify-influencer-marketing','creator-seeding'],
            ],
            'micro-influencer-marketing' => [
                'name'      => 'Micro Influencer Marketing',
                'short'     => 'micro influencer marketing',
                'tagline'   => '10K–100K creators with the highest engagement + conversion rates.',
                'emoji'     => '⭐',
                'grad'      => 'from-amber-500 to-orange-500',
                'accent'    => '#f59e0b',
                'intent'    => 'You want authenticity + reach at DTC-friendly prices. Micro creators outperform macro on CTR by 2.4×.',
                'good_for'  => ['Bootstrapped DTC brands', 'Regional campaigns', 'Brands that value ER > follower count'],
                'kpi'       => [['10K–100K', 'Follower range'], ['8.4%', 'Avg ER'], ['₹5K–₹40K', 'Per creator']],
                'related_service_slugs' => ['nano-influencer-marketing','ugc-influencers','barter-influencers','instagram-influencer-marketing'],
            ],
            'nano-influencer-marketing' => [
                'name'      => 'Nano Influencer Marketing',
                'short'     => 'nano influencer marketing',
                'tagline'   => '1K–10K creators — hyper-local, hyper-loyal, hyper-engaged.',
                'emoji'     => '✨',
                'grad'      => 'from-cyan-500 to-emerald-500',
                'accent'    => '#06b6d4',
                'intent'    => 'You want community-first reach for local drops, launches, or store visits.',
                'good_for'  => ['Local brands + F&B outlets', 'Hyper-local product launches', 'Very early-stage brands'],
                'kpi'       => [['1K–10K', 'Follower range'], ['12.1%', 'Avg ER'], ['Barter', 'Standard']],
                'related_service_slugs' => ['micro-influencer-marketing','barter-influencers','store-visit-campaigns'],
            ],
            'creator-marketplace' => [
                'name'      => 'Creator Marketplace',
                'short'     => 'creator marketplace',
                'tagline'   => 'Browse 100K+ verified creators — filter, shortlist, invite in minutes.',
                'emoji'     => '🎯',
                'grad'      => 'from-indigo-500 to-violet-500',
                'accent'    => '#6366f1',
                'intent'    => 'You want a searchable database of creators with real audience data.',
                'good_for'  => ['In-house influencer teams', 'Agencies managing multiple brands', 'Founders doing DIY campaigns'],
                'kpi'       => [['100K+', 'Verified creators'], ['12+', 'Filters'], ['Live', 'Audience data']],
                'related_service_slugs' => ['influencer-marketing-agency','ugc-influencers','micro-influencer-marketing'],
            ],
            'paid-influencer-campaigns' => [
                'name'      => 'Paid Influencer Campaigns',
                'short'     => 'paid influencer campaigns',
                'tagline'   => 'Paid creator deals with contracts, attribution and payouts built-in.',
                'emoji'     => '💸',
                'grad'      => 'from-emerald-500 to-cyan-500',
                'accent'    => '#10b981',
                'intent'    => 'You want to pay creators (cash) for higher-quality, exclusive content.',
                'good_for'  => ['Scaling brands', 'Category leaders needing premium creative', 'Campaigns requiring exclusivity'],
                'kpi'       => [['Contracts', 'Auto'], ['Escrow', 'Included'], ['Attribution', 'Live']],
                'related_service_slugs' => ['ugc-influencers','influencer-marketing-agency','creator-videoshoot'],
            ],
            'shopify-influencer-marketing' => [
                'name'      => 'Shopify Influencer Marketing',
                'short'     => 'Shopify influencer marketing',
                'tagline'   => 'Native Shopify sync — auto orders, unique codes, tracked revenue.',
                'emoji'     => '🛍',
                'grad'      => 'from-lime-500 to-emerald-500',
                'accent'    => '#84cc16',
                'intent'    => 'You run a Shopify store and want creator campaigns baked into your existing stack.',
                'good_for'  => ['Shopify DTC brands', 'Brands doing bulk seeding', 'Teams needing real attribution'],
                'kpi'       => [['Shopify app', 'Native'], ['Auto', 'Orders + codes'], ['Multi-touch', 'Attribution']],
                'related_service_slugs' => ['influencer-marketing-agency','barter-influencers','ugc-influencers','creator-seeding'],
            ],
            'creator-seeding' => [
                'name'      => 'Creator Seeding',
                'short'     => 'creator seeding',
                'tagline'   => 'Ship product to 100s of creators. Waitlists, tracking, content approval — automated.',
                'emoji'     => '📦',
                'grad'      => 'from-violet-500 to-fuchsia-500',
                'accent'    => '#8b5cf6',
                'intent'    => 'You want to seed products at scale (50-500 creators) with zero ops.',
                'good_for'  => ['Beauty, fashion, food brands', 'Product launches', 'Content library plays'],
                'kpi'       => [['500+', 'Creators / seed'], ['Auto', 'Order distribution'], ['62%', 'Accept rate']],
                'related_service_slugs' => ['barter-influencers','shopify-influencer-marketing','micro-influencer-marketing'],
            ],
            'creator-videoshoot' => [
                'name'      => 'Creator Videoshoot',
                'short'     => 'creator video shoot',
                'tagline'   => 'Studio-grade UGC video shoots for paid social — multi-format, full rights.',
                'emoji'     => '🎥',
                'grad'      => 'from-rose-500 to-orange-500',
                'accent'    => '#f43f5e',
                'intent'    => 'You need premium UGC video with directed hooks + multiple aspect ratios.',
                'good_for'  => ['Paid performance teams', 'DTC scaling on Meta / TikTok Ads', 'Brands needing ad-ready creative'],
                'kpi'       => [['3–5 videos', 'Per creator'], ['3', 'Aspect ratios'], ['14 days', 'Turnaround']],
                'related_service_slugs' => ['ugc-influencers','paid-influencer-campaigns','influencer-marketing-agency'],
            ],
            'instagram-influencer-marketing' => [
                'name'      => 'Instagram Influencer Marketing',
                'short'     => 'Instagram influencer marketing',
                'tagline'   => 'Reels, stories, carousels — with real engagement + revenue tracking.',
                'emoji'     => '📸',
                'grad'      => 'from-pink-500 to-purple-500',
                'accent'    => '#ec4899',
                'intent'    => 'You want Instagram-first campaigns with attribution beyond likes.',
                'good_for'  => ['DTC beauty, fashion, F&B brands', 'Brands with high IG-first buyer intent', 'Reel-heavy content strategies'],
                'kpi'       => [['Reels', '#1 format'], ['8.1%', 'Avg ER'], ['Multi-touch', 'Attribution']],
                'related_service_slugs' => ['ugc-influencers','micro-influencer-marketing','barter-influencers','creator-marketplace'],
            ],
        ];
    }

    /**
     * Top Indian metros (+ a national "india" bucket). Slugs are lowercase-hyphenated.
     */
    public static function cities(): array
    {
        return [
            'delhi'     => ['name' => 'Delhi',     'region' => 'Delhi NCR', 'lat' => 28.6139, 'lng' => 77.2090, 'pop' => '32M metro',  'note' => 'India\'s largest creator hub after Mumbai — 22K+ active creators.'],
            'mumbai'    => ['name' => 'Mumbai',    'region' => 'Maharashtra','lat' => 19.0760, 'lng' => 72.8777, 'pop' => '21M metro',  'note' => 'India\'s #1 creator city — fashion, beauty, lifestyle strongholds.'],
            'bangalore' => ['name' => 'Bangalore', 'region' => 'Karnataka', 'lat' => 12.9716, 'lng' => 77.5946, 'pop' => '13M metro',  'note' => 'Tech + startup + wellness creator density.'],
            'hyderabad' => ['name' => 'Hyderabad', 'region' => 'Telangana', 'lat' => 17.3850, 'lng' => 78.4867, 'pop' => '10M metro',  'note' => 'Fastest-growing regional creator base in South India.'],
            'chennai'   => ['name' => 'Chennai',   'region' => 'Tamil Nadu','lat' => 13.0827, 'lng' => 80.2707, 'pop' => '11M metro',  'note' => 'Regional-language creator strength — Tamil content dominates.'],
            'pune'      => ['name' => 'Pune',      'region' => 'Maharashtra','lat' => 18.5204, 'lng' => 73.8567, 'pop' => '7M metro',   'note' => 'Young college-town creator base — lifestyle + fitness heavy.'],
            'kolkata'   => ['name' => 'Kolkata',   'region' => 'West Bengal','lat' => 22.5726, 'lng' => 88.3639, 'pop' => '15M metro',  'note' => 'Bengali-language creator strength — food + culture strongholds.'],
            'ahmedabad' => ['name' => 'Ahmedabad', 'region' => 'Gujarat',   'lat' => 23.0225, 'lng' => 72.5714, 'pop' => '8M metro',    'note' => 'Fast-growing Gujarati creator base — DTC + retail.'],
            'jaipur'    => ['name' => 'Jaipur',    'region' => 'Rajasthan', 'lat' => 26.9124, 'lng' => 75.7873, 'pop' => '4M metro',    'note' => 'Fashion + travel + jewellery creator specialty.'],
            'gurugram'  => ['name' => 'Gurugram',  'region' => 'Delhi NCR', 'lat' => 28.4595, 'lng' => 77.0266, 'pop' => '2.5M metro',  'note' => 'Corporate + lifestyle creator base — high-CPM audience.'],
            'india'     => ['name' => 'India',     'region' => 'India',     'lat' => 20.5937, 'lng' => 78.9629, 'pop' => '1.4B',         'note' => 'National coverage — every city, every language, every niche.'],
        ];
    }

    public static function service(string $slug): ?array
    {
        return static::services()[$slug] ?? null;
    }

    public static function city(string $slug): ?array
    {
        return static::cities()[$slug] ?? null;
    }

    public static function faqs(array $service, ?array $city = null): array
    {
        $name = $service['name'];
        $loc  = $city ? $city['name'] : 'India';

        return [
            [
                'q' => "How much does {$service['short']} cost in {$loc}?",
                'a' => "Barter (product-only) campaigns cost only the retail value of seeded items — often ₹0 in cash. Paid {$service['short']} in {$loc} ranges from ₹5,000 for nano-creators (1K–10K followers) to ₹1.5L+ for macro creators. CreatorPlex's rate calculator gives fair benchmarks for your niche.",
            ],
            [
                'q' => "Which brands use CreatorPlex for {$service['short']}" . ($city ? " in {$city['name']}" : '') . "?",
                'a' => "Over 1,000 DTC brands run {$service['short']} on CreatorPlex — including {$loc}-based brands across beauty, fashion, food, tech, home and travel. See sample work on our home page.",
            ],
            [
                'q' => "Do I need a Shopify store to run {$service['short']}?",
                'a' => 'No. CreatorPlex works with Shopify (native sync), WooCommerce, Amazon, CSV, or manual product entry. Shopify unlocks automatic order + inventory sync but is optional.',
            ],
            [
                'q' => "How is {$service['short']} performance measured?",
                'a' => "Every creator gets a unique discount code + referral link. Attributed revenue, orders, CVR, ER, and ROAS show in real time on your campaign page. Multi-touch attribution rolls up assisted conversions too.",
            ],
            [
                'q' => "How fast can I launch a {$service['short']} campaign?",
                'a' => "Most brands launch their first campaign within 24 hours of signup. AI briefs cut creative direction time from days to minutes.",
            ],
            [
                'q' => "Does CreatorPlex handle contracts and payouts?",
                'a' => "Yes. Contracts are auto-generated per creator and cover usage rights, deliverables and payment. Payouts run through Stripe Connect with a configurable escrow hold.",
            ],
            [
                'q' => "Can I run {$service['short']} in regional languages" . ($city ? " like {$city['region']}" : '') . "?",
                'a' => "Yes. Filter creators by language, region, or city. India-focused brands typically run parallel campaigns in Hindi + English + regional languages.",
            ],
        ];
    }
}
