<?php

namespace Database\Seeders;

use App\Models\BlogPost;
use App\Support\SchemaCheck;
use Illuminate\Database\Seeder;

class SeoBlogSeeder extends Seeder
{
    public function run(): void
    {
        if (! SchemaCheck::has('blog_posts')) {
            $this->command?->warn('blog_posts table not migrated — skipping SEO blog seed.');
            return;
        }

        $posts = [
            [
                'slug'     => 'influencer-marketing-agency-delhi-cost',
                'title'    => 'How much does an influencer marketing agency in Delhi cost in 2026?',
                'category' => 'Playbooks',
                'excerpt'  => 'A transparent breakdown of what Delhi-based DTC brands actually pay for influencer campaigns — retainer vs. per-campaign, barter vs. paid, and the hidden costs to watch.',
                'cover_gradient' => 'from-violet-500 to-pink-500',
                'read_minutes' => '9 min',
                'meta_title'   => 'Influencer Marketing Agency Cost in Delhi (2026 Guide) · CreatorPlex',
                'meta_description' => "What a Delhi influencer marketing agency actually costs — barter vs. paid, retainer vs. per-campaign, and the hidden fees to watch out for. Real numbers from 12,000+ deals.",
                'faq_json' => [
                    ['q' => 'Which is cheaper — a Delhi influencer marketing agency or hiring in-house?', 'a' => 'For most DTC brands under ₹5Cr revenue, an agency (or platform like CreatorPlex) is 40-60% cheaper than in-house. A single in-house influencer marketer costs ₹8-15L/year fully loaded; a growth-tier CreatorPlex account is ₹149-499/mo.'],
                    ['q' => 'What\'s a fair retainer for a Delhi agency?', 'a' => 'For 4-6 campaigns per month with 20-30 creators each, retainers range ₹50K-₹1.5L/mo. Below that, per-campaign pricing works better.'],
                    ['q' => 'Do Delhi agencies handle GST invoicing correctly?', 'a' => 'Reputable ones do. CreatorPlex generates GST-compliant invoices with GSTIN and reverse-charge logic built in.'],
                ],
            ],
            [
                'slug'  => 'best-ugc-influencers-delhi-brands',
                'title' => 'The best UGC influencers in Delhi for DTC brands (2026)',
                'category' => 'Guides',
                'excerpt'  => 'Delhi has 22K+ active creators. Here\'s how to find the ones who actually convert — filters, benchmarks, and the exact playbook we use.',
                'cover_gradient' => 'from-emerald-500 to-teal-500',
                'read_minutes' => '7 min',
                'meta_title'   => 'Best UGC Influencers in Delhi for DTC Brands (2026) · CreatorPlex',
                'meta_description' => "Delhi has 22K+ active creators. Here's the exact filter, benchmark, and playbook we use to find UGC influencers who convert.",
                'faq_json' => [
                    ['q' => 'How many UGC influencers does Delhi have?', 'a' => 'CreatorPlex tracks 22,000+ active creators based in Delhi NCR. Of those, roughly 8,400 are UGC-focused (video-first, 15-45s formats).'],
                    ['q' => 'What\'s a fair UGC video rate in Delhi?', 'a' => 'Nano (1-10K followers): ₹2,000-₹8,000. Micro (10-100K): ₹8,000-₹40,000. Mid (100K-1M): ₹40K-₹1.5L. Use CreatorPlex\'s free rate calculator.'],
                    ['q' => 'Can I get barter UGC in Delhi?', 'a' => 'Yes — 62% average acceptance rate across categories. Beauty and F&B run 70%+ acceptance on barter.'],
                ],
            ],
            [
                'slug' => 'barter-influencer-marketing-guide-india',
                'title' => 'Barter influencer marketing in India: the complete 2026 guide',
                'category' => 'Playbooks',
                'excerpt' => 'From zero cash to 4-8× ROAS — the exact barter playbook 1,000+ DTC brands run on CreatorPlex.',
                'cover_gradient' => 'from-pink-500 to-rose-500',
                'read_minutes' => '11 min',
                'meta_title'   => 'Barter Influencer Marketing in India · 2026 Guide · CreatorPlex',
                'meta_description' => "Zero cash, 62% accept rate, 4-8× ROAS. The complete barter influencer marketing playbook for DTC brands in India.",
                'faq_json' => [
                    ['q' => 'What is barter influencer marketing?', 'a' => 'You send free product to the creator in exchange for content. Zero cash. The creator gets a product they\'d have bought anyway; the brand gets authentic UGC + reach at just the retail value of the item.'],
                    ['q' => 'Do Indian creators really accept barter?', 'a' => 'Yes — 62% average across CreatorPlex India. Beauty hits 78%, food 65%, fashion 55%. Higher when the product is genuinely desirable and matches the creator\'s niche.'],
                    ['q' => 'Is barter influencer marketing legal in India?', 'a' => 'Yes. It\'s treated as barter under the Income Tax Act. Both sides recognize the market value of exchanged goods/services. CreatorPlex contracts codify this.'],
                    ['q' => 'How many creators should I seed for a launch?', 'a' => 'For a launch: 30-50 creators. For maintenance: 10-15/month. CreatorPlex auto-invites 3× your target based on a 30% accept-rate assumption.'],
                ],
            ],
            [
                'slug' => 'shopify-influencer-marketing-attribution-india',
                'title' => 'How to attribute Shopify sales to influencer campaigns (India)',
                'category' => 'Engineering',
                'excerpt' => 'Every Indian DTC brand asks: "which creator drove that sale?" Here\'s the exact code + UTM + multi-touch setup we use.',
                'cover_gradient' => 'from-lime-500 to-emerald-500',
                'read_minutes' => '8 min',
                'meta_title'   => 'Shopify Influencer Attribution in India · CreatorPlex',
                'meta_description' => "Unique discount codes + UTM referral links + multi-touch attribution. The complete Shopify influencer attribution setup for Indian DTC brands.",
                'faq_json' => [
                    ['q' => 'How do I attribute Shopify sales to influencers?', 'a' => 'Three layers: (1) unique discount code per creator (last-click), (2) UTM referral link (assisted), (3) multi-touch attribution across the buyer journey. CreatorPlex ships all three on install.'],
                    ['q' => 'Does Shopify have native creator attribution?', 'a' => 'No — Shopify tracks the code and the referrer separately, but doesn\'t roll them up per creator. That\'s what apps like CreatorPlex add.'],
                    ['q' => 'What about creators who post organically without a code?', 'a' => 'Multi-touch attribution catches those — even if the buyer eventually converts on a different visit, we can trace the first-touch back to the creator\'s content view.'],
                ],
            ],
            [
                'slug' => 'ugc-influencers-mumbai-vs-delhi',
                'title' => 'UGC influencers in Mumbai vs Delhi: where should you seed?',
                'category' => 'Benchmarks',
                'excerpt' => 'Mumbai has the highest creator density; Delhi has the highest brand demand. Here\'s the data-backed answer.',
                'cover_gradient' => 'from-indigo-500 to-violet-500',
                'read_minutes' => '6 min',
                'meta_title'   => 'UGC Influencers: Mumbai vs Delhi Comparison · CreatorPlex',
                'meta_description' => "Mumbai vs Delhi for UGC influencer campaigns — which city has better rates, higher ER, faster acceptance? Data from 12,000+ deals.",
                'faq_json' => [
                    ['q' => 'Which city has more active creators?', 'a' => 'Mumbai leads India with ~28K active creators; Delhi is #2 with ~22K. Bangalore is #3 with ~14K.'],
                    ['q' => 'Which city has cheaper creators?', 'a' => 'Delhi averages 12-18% cheaper than Mumbai for equivalent follower tiers, especially in the micro (10K-100K) bracket.'],
                    ['q' => 'Which city has higher ER?', 'a' => 'Delhi averages 7.2% ER vs Mumbai\'s 6.5% — but Mumbai has better follower quality (fewer bot audiences).'],
                ],
            ],
        ];

        foreach ($posts as $p) {
            BlogPost::updateOrCreate(
                ['slug' => $p['slug']],
                $p + ['body' => "# {$p['title']}\n\n{$p['excerpt']}\n\n*Full article coming soon.*", 'is_published' => true, 'published_at' => now()],
            );
        }

        $this->command?->info('Seeded '.count($posts).' SEO blog posts.');
    }
}
