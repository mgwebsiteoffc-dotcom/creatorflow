<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class MarketingController extends Controller
{
    public function features()  { return view('marketing.features'); }
    public function pricing()   { return view('marketing.pricing'); }
    public function about()     { return view('marketing.about'); }
    public function contact()   { return view('marketing.contact'); }

    public function industryShow(string $slug)
    {
        $industries = static::industryData();
        $item = $industries[$slug] ?? null;
        abort_unless($item, 404);
        return view('marketing.industry', ['slug' => $slug, 'item' => $item, 'all' => $industries]);
    }

    public function campaignTypeShow(string $slug)
    {
        $types = static::campaignTypeData();
        $item = $types[$slug] ?? null;
        abort_unless($item, 404);
        return view('marketing.campaign-type', ['slug' => $slug, 'item' => $item, 'all' => $types]);
    }

    public static function industryData(): array
    {
        return [
            'fashion-and-lifestyle' => [
                'title' => 'Fashion &amp; Lifestyle',
                'tagline' => 'Aesthetic drops, real ER, viral hauls',
                'emoji' => '👗', 'grad' => 'from-fuchsia-500 to-pink-500',
                'why'   => 'Fashion buyers trust creators over ads by 3.2×. Seeding 30 micro-creators generates 100+ UGC assets you can turn into paid.',
                'stats' => [['Avg ER', '6.8%'], ['Avg ROAS', '5.4×'], ['Best format', 'Reels']],
                'meta'  => 'Run fashion & lifestyle influencer campaigns on CreatorFlow — AI-matched creators, bulk seeding, live attribution. Perfect for DTC apparel and lifestyle brands in India and globally.',
                'body'  => "## Why CreatorFlow for fashion &amp; lifestyle brands\n\nFashion is an **emotion buy**. Buyers convert when a creator they trust wears the piece in a real setting — a coffee run, a mirror haul, a friend's wedding. CreatorFlow powers the entire loop: match, seed, ship, review, attribute.\n\n### What great fashion campaigns look like\n- **Micro-creators outperform mega-influencers** for CTR by ~2.4× in apparel\n- **Reel + carousel combo** is the highest-performing format for fashion (avg 6.8% ER on CreatorFlow)\n- **Barter + 5% commission** beats pure paid on ROAS by 3.1×\n\n### The playbook we recommend\n1. Pick your **hero SKU** — one silhouette, 2-3 colorways\n2. Filter creators by audience overlap ≥ 40% with your buyer persona\n3. Seed **30 creators** across 3 tiers (10 nano · 15 micro · 5 mid)\n4. Enable your **unique discount code** on Shopify (auto-generated)\n5. Watch content roll in; approve inside the platform; content library ready for paid social\n\n### Real brand examples\n- **Roving Mode** — 40 seeded creators, 60 UGC assets, ₹4.2L attributed revenue in 30 days\n- **Evereve** — barter-only campaign, 780K reach, 12% CVR on new-visitor traffic",
                'faqs'  => [
                    ['q' => 'How much does a fashion influencer campaign cost in India?', 'a' => 'Barter (product-only) campaigns cost you just the retail value of the seeded items. Paid campaigns typically range from ₹5,000 for nano-creators (10K–50K followers) to ₹80,000+ for macro fashion creators. CreatorFlow\'s rate calculator gives fair benchmarks.'],
                    ['q' => 'Do creators need to have a Shopify account?', 'a' => 'No. Creators sign up free on CreatorFlow. When a brand approves them, CreatorFlow generates a unique discount code and (for Shopify brands) automatically creates the seeding order with tracking.'],
                    ['q' => 'How is fashion campaign performance measured?', 'a' => 'Every creator gets a unique code + referral link. Attributed revenue, orders, CVR, ER, and ROAS are shown in real-time on your campaign page. You can export CSV for finance.'],
                    ['q' => 'Can I run barter-only fashion campaigns?', 'a' => 'Yes. In fact 68% of fashion campaigns on CreatorFlow are barter-only. Set the campaign type to Barter and the creator fee to 0.'],
                    ['q' => 'What sizes and product types work best?', 'a' => 'Wear-first categories (tees, dresses, activewear) convert fastest. Accessories (jewellery, eyewear) work great for micro-creator seeding thanks to lower per-unit cost.'],
                ],
            ],
            'beauty-and-cosmetics' => [
                'title' => 'Beauty &amp; Cosmetics',
                'tagline' => 'Skin-first content, honest reviews, quick trials',
                'emoji' => '💄', 'grad' => 'from-rose-500 to-orange-500',
                'why'   => 'Beauty is the #1 seeding category on CreatorFlow. Acceptance rates hit 62% on average and CVR beats every other niche.',
                'stats' => [['Avg ER', '8.1%'], ['Avg ROAS', '7.2×'], ['Best format', 'GRWM Reels']],
                'meta'  => 'Grow your beauty brand with creator-led UGC. CreatorFlow helps skincare, makeup and cosmetics brands seed 100s of creators, review content, and attribute sales in real time.',
                'body'  => "## Beauty runs on trust — and creators are the trust layer\n\nBuyers scroll for **honest** reactions: swatches, before/afters, GRWM (Get Ready With Me) reels. The **shelf** you can't buy: authentic content that shows results.\n\n### Why beauty over-indexes on CreatorFlow\n- **62% average acceptance rate** — creators genuinely want beauty products\n- **8.1% average ER** on beauty content — highest of any niche\n- **7.2× ROAS** on paid + code campaigns\n- Content library from one 30-creator seed = 90+ ad-ready assets\n\n### Playbook — from launch to scale\n1. **Launch week**: seed 50 nano-creators for maximum volume + awareness\n2. **Weeks 2-4**: paid campaigns with the top 5 performers, whitelist for ads\n3. **Month 2**: expand into review + tutorial content\n4. **Ongoing**: quarterly barter drops for new SKUs\n\n### AI content review keeps you safe\nMakeup and skincare have compliance risks (medical claims, disclosure). CreatorFlow's AI review flags unverified claims and missing disclosures **before** the content goes live.",
                'faqs'  => [
                    ['q' => 'Do beauty creators disclose that content is sponsored?', 'a' => 'Yes. CreatorFlow contracts include #ad / #paidpartnership requirements per platform. Our AI content review flags posts that miss disclosure.'],
                    ['q' => 'Which beauty products work best for seeding?', 'a' => 'Everyday-use SKUs with visible results: serums, lipsticks, mascaras, foundations. Fragrance seeding works but takes better creative direction because you can\'t "show" the product working.'],
                    ['q' => 'How do I run FDA / regulatory-compliant campaigns?', 'a' => 'Add compliance rules to your campaign brief (no medical claims, no comparison to competitors by name, etc). Our AI content review flags violations before content is approved.'],
                    ['q' => 'Can I invite verified beauty influencers only?', 'a' => 'Yes. Filter the creator marketplace by "verified" and by niche = Beauty &amp; Skincare. All CreatorFlow verified creators have passed identity + platform verification.'],
                    ['q' => 'What\'s the typical timeline from launch to first content?', 'a' => 'Beauty campaigns have the shortest cycle. Average 4-7 days from launch to first content live. Fast because creators want to try new beauty products immediately.'],
                ],
            ],
            'food-and-fitness' => [
                'title' => 'Food &amp; Fitness',
                'tagline' => 'Recipes, workouts, sampling that converts',
                'emoji' => '🥗', 'grad' => 'from-emerald-500 to-teal-500',
                'why'   => 'Foodies over-index on repeat purchase. Fitness creators unlock high-intent cohorts that scale into subscriptions.',
                'stats' => [['Avg ER', '9.4%'], ['Avg ROAS', '4.6×'], ['Best format', 'Tutorial']],
                'meta'  => 'Run food, beverage, wellness and fitness influencer campaigns with CreatorFlow — sampling, tutorials, before/after content, all with real revenue attribution.',
                'body'  => "## Food + fitness convert on demonstration\n\nUnlike fashion (aesthetic) or beauty (trust), food and fitness convert on **proof**. Show it working. Show the recipe. Show the transformation.\n\n### What works\n- **Recipe reels** for packaged food, sauces, condiments, mixes\n- **Meal-prep tutorials** for supplements, protein, healthy snacks\n- **60-day transformations** for fitness gear + apparel\n- **Store-visit content** for restaurants, cafes, fitness studios\n\n### Repeat purchase = lifetime value\nFood/wellness has the highest **repeat purchase rate** of any category on CreatorFlow (avg 3.2× within 90 days). Even a modest first-order ROAS compounds massively when your LTV is 3× your CAC.\n\n### Playbook\n1. Seed 20 creators with a **7-day supply** — enough to demonstrate results\n2. Ask for **process content** (not just the end state)\n3. Follow up at day 30 with a **paid whitelisting** deal for top-performers\n4. Enable **subscription discount** on the checkout for creator codes",
                'faqs'  => [
                    ['q' => 'Do you support restaurant and cafe store-visit campaigns?', 'a' => 'Yes. Use the "Store visit" campaign type. Filter creators by city, invite them for a visit, they film in-store, you get local reach and footfall.'],
                    ['q' => 'What about fitness supplement compliance?', 'a' => 'Same as beauty — add compliance rules to your brief (no medical claims, no false transformation promises). AI content review enforces them.'],
                    ['q' => 'Can creators show cooking with the product?', 'a' => 'Yes — recipe reels are the highest-performing content type for food brands. Provide a recipe brief; creators shoot the process.'],
                    ['q' => 'Do fitness creators need proof of results?', 'a' => 'For long-form testimonials, yes. For general product placement (apparel, gear), no proof needed — just clear brand tags and honest usage.'],
                    ['q' => 'What\'s the best campaign type for beverage brands?', 'a' => 'Barter seeding at scale (50+ creators) works phenomenally for beverages. Low per-unit cost, high content volume, and content is inherently shareable.'],
                ],
            ],
            'tech-and-education' => [
                'title' => 'Tech &amp; Education',
                'tagline' => 'Long-form trust, demo-first video',
                'emoji' => '💻', 'grad' => 'from-cyan-500 to-blue-500',
                'why'   => 'YouTube integrations for tech buyers hit 9× ROAS. EdTech converts best via 30-45s hook + carousel.',
                'stats' => [['Avg ER', '4.2%'], ['Avg ROAS', '9.1×'], ['Best format', 'YT integration']],
                'meta'  => 'Tech and EdTech brands: run creator campaigns that convert. Long-form YouTube integrations, tutorial reels, and demo-first content with real attribution.',
                'body'  => "## Tech buyers want proof, not promises\n\nSoftware, gadgets, and EdTech buyers spend **hours in research** before they convert. Your creator strategy needs to match: **long-form YouTube integrations** or **detailed tutorial reels** — not lifestyle drops.\n\n### Best formats for tech\n- **YouTube integrations** (60-second segment inside a related review) — 9.1× ROAS average\n- **Twitter threads** with screenshots and use cases\n- **Reddit AMA style** (creator plays reviewer, brand answers)\n- **Tutorial reels** for tools with a clear \"aha\" moment\n\n### EdTech: 3-second hook is everything\nFor courses, the hook has to promise a concrete outcome (\"How I learned to code in 6 months\"), then let the creator introduce the tool naturally. Don't script — CreatorFlow generates AI briefs that give creative direction without killing authenticity.",
                'faqs'  => [
                    ['q' => 'Which platforms work best for SaaS brands?', 'a' => 'YouTube for high-consideration, Twitter/X for developer tools, LinkedIn for B2B, TikTok for consumer tools. CreatorFlow supports all four with platform-native brief templates.'],
                    ['q' => 'How do I attribute SaaS trial signups to creators?', 'a' => 'Unique referral URLs with UTM tracking. CreatorFlow generates them per creator and rolls up conversions in the analytics tab.'],
                    ['q' => 'Can I run campaigns for a hardware product?', 'a' => 'Yes — hardware seeding works great. Ship the device, wait for unboxing + first-30-day content. AI content review flags missing key specs / disclaimers.'],
                    ['q' => 'What about EdTech and course sales?', 'a' => 'EdTech is one of our fastest-growing segments. Barter (free course access for creators) + revenue share unlocks scale.'],
                    ['q' => 'Do creators need technical expertise for tech campaigns?', 'a' => 'Only for deeply technical products. For consumer tech (apps, wearables, cameras), a general lifestyle creator with tech-curious audience often outperforms a pure tech reviewer.'],
                ],
            ],
            'travel-and-hospitality' => [
                'title' => 'Travel &amp; Hospitality',
                'tagline' => 'Destination reels, hotel walkthroughs, itineraries',
                'emoji' => '✈️', 'grad' => 'from-sky-500 to-indigo-500',
                'why'   => 'Barter-only campaigns (property stays) drive massive volume — 40 creators for zero cash outlay is standard.',
                'stats' => [['Avg ER', '5.6%'], ['Avg ROAS', '3.8×'], ['Best format', 'POV Reel']],
                'meta'  => 'Hotels, resorts, tourism boards, and travel brands: run creator campaigns via CreatorFlow with barter stays, POV reels, and destination content.',
                'body'  => "## Travel is a barter goldmine\n\nA free 2-night stay costs you the empty-room-marginal-cost. To a mid-tier travel creator, it's a ₹40,000+ perceived value. That asymmetry is why **68% of travel campaigns on CreatorFlow are barter-only**.\n\n### Playbook\n1. Pick your **off-season weekdays** for barter stays\n2. Invite creators with **travel-only audiences** (skip generic lifestyle)\n3. Ask for **POV walkthrough reels** + **stories** + **1 carousel**\n4. Add a **discount code** for their followers → measurable bookings\n\n### Real numbers\nA boutique hotel we seeded ran 12 creators over 3 months, generated 8.4M reach, and booked ₹9.2L direct revenue attributed to creator codes. Zero cash paid.",
                'faqs'  => [
                    ['q' => 'How do I structure a barter stay?', 'a' => 'Free room + 2 meals for 2 nights, in exchange for 1 reel + 5 stories + 1 IG carousel. All standard in CreatorFlow contracts.'],
                    ['q' => 'What about international creators?', 'a' => 'Fully supported. Filter creators by country in the marketplace. Payouts (if paid) route through the creator\'s local currency automatically.'],
                    ['q' => 'Can travel brands run store-visit campaigns?', 'a' => 'Yes — use the "Store visit" campaign type. Creators check in on Google/Foursquare, generate footfall and reviews.'],
                    ['q' => 'How is booking attribution done?', 'a' => 'Unique discount codes at checkout, and creator-specific landing pages if you integrate deep-linking. Simple codes hit 90% of the attribution need.'],
                    ['q' => 'What formats work best?', 'a' => 'POV walkthroughs (reel format), aerial drone shots (for resorts), and honest room-tour stories. Skip staged fashion-style content.'],
                ],
            ],
            'home-and-decor' => [
                'title' => 'Home &amp; Decor',
                'tagline' => 'Room tours, hauls, before/after transformations',
                'emoji' => '🏠', 'grad' => 'from-amber-500 to-yellow-500',
                'why'   => 'DIY & decor tags drive the highest saves rate on IG. Great for evergreen catalog seeding.',
                'stats' => [['Avg ER', '6.1%'], ['Avg ROAS', '4.2×'], ['Best format', 'Before/After']],
                'meta'  => 'Home & decor brands: seed creators for room tours, before/afters, and DIY content. CreatorFlow handles matching, briefs, shipping, and attribution end-to-end.',
                'body'  => "## Decor is evergreen — content library compounds\n\nUnlike beauty (fast news cycle) or fashion (seasonal), home/decor content **stays valuable** for 6+ months. A great room-tour reel keeps driving discovery long after it posts. This makes seeding at scale a **content library play** — every asset works for months.\n\n### What works\n- **Before / After** transformations (highest save rate on IG)\n- **Room tour reels** with product placement\n- **DIY tutorials** using the product\n- **Amazon-style hauls** for furniture, decor, kitchenware brands",
                'faqs'  => [
                    ['q' => 'Do you support furniture (large item) seeding?', 'a' => 'Yes — but the acceptance rate is lower and shipping is more logistical. Filter for creators with confirmed home-owner audiences. Reach out to us for large-item playbooks.'],
                    ['q' => 'Can I run "one product, 20 creators" seeds?', 'a' => 'Yes — this is called single-SKU seeding. CreatorFlow handles the order distribution, tracking, and content approval flow automatically.'],
                    ['q' => 'How do I get before/after content?', 'a' => 'Include it as a required deliverable in your brief. AI content review flags submissions missing the deliverable and asks the creator for a change.'],
                    ['q' => 'What creator types work for home brands?', 'a' => 'Home + lifestyle + first-home creators over-index. Skip fashion/beauty creators — different buyer intent.'],
                    ['q' => 'Do returns/exchanges get handled?', 'a' => 'Barter seeded items are non-returnable by contract. Any exchanges (wrong size, defective) are handled through the standard messaging thread.'],
                ],
            ],
        ];
    }

    public static function campaignTypeData(): array
    {
        return [
            'product-review' => [
                'title' => 'Product Review',
                'tagline' => 'Honest video reviews from verified creators',
                'emoji' => '⭐', 'grad' => 'from-amber-500 to-orange-500',
                'why'   => 'Build trust and drive awareness with authentic creator reviews. Perfect for launches and new SKUs.',
                'bullets' => ['3–5 minute in-depth video', 'Pros / cons format', 'AI content review for compliance'],
                'meta'  => 'Run authentic product review campaigns with verified creators. AI briefs, contracts, and attribution — all on CreatorFlow.',
                'body'  => "## When to run a product review campaign\n\nProduct reviews are the **trust-building** creator format. Best for:\n- **New product launches** — validate your positioning through creator voice\n- **Category-crowded SKUs** — help buyers pick you over competitors\n- **High-consideration items** — tech, appliances, subscriptions\n\n### What the deliverable looks like\n- 3–5 minute in-depth video (YouTube, TikTok, IG Reel)\n- **Honest pros/cons** format (creator's real opinion — don't script)\n- Product used for at least 7 days before review\n- Discount code + affiliate link in description\n\n### Pricing\n- **Nano** (10K-50K): ₹3,000–₹10,000\n- **Micro** (50K-250K): ₹10,000–₹40,000\n- **Mid** (250K-1M): ₹40,000–₹1.5L\n- **Macro** (1M+): ₹1.5L+\n\n### Why CreatorFlow\n- **AI compliance review** flags unverified claims\n- **Video-approval workflow** — approve, request changes, or reject\n- **Attribution built-in** — see how many buyers each review converts",
                'faqs'  => [
                    ['q' => 'How long does a product review campaign take?', 'a' => 'Typical timeline: Day 0 launch → Day 3 creator applies → Day 5 approved + shipped → Day 12 review posted. So around 2 weeks end-to-end.'],
                    ['q' => 'Can I request changes to the review?', 'a' => 'Yes. Content submission goes through an approval flow: you can approve, request changes with a comment, or (rarely) reject. Creators expect this and it\'s covered in the CreatorFlow contract.'],
                    ['q' => 'What if the creator says something negative?', 'a' => 'Honest reviews mean occasional criticism. That\'s actually good for conversion — audiences trust balanced reviews. AI content review flags anything factually wrong (medical claims, false specs) so you can request corrections.'],
                    ['q' => 'How is the discount code tracked?', 'a' => 'Each creator gets a unique Shopify discount code auto-generated at approval time. Every use is attributed on the campaign analytics page.'],
                    ['q' => 'Are usage rights included?', 'a' => 'Standard CreatorFlow contract gives you 90 days of paid + organic usage rights. Extended usage is negotiable per campaign.'],
                ],
            ],
            'brand-awareness' => [
                'title' => 'Brand Awareness',
                'tagline' => 'Reach + impressions at scale',
                'emoji' => '📣', 'grad' => 'from-violet-500 to-fuchsia-500',
                'why'   => 'Story-led campaigns designed to leave a mark. Reach the right audience at scale in their feed.',
                'bullets' => ['15–45s hero video', 'Multi-creator burst', 'ER-weighted matching'],
                'meta'  => 'Scale brand awareness with a multi-creator burst campaign. Reach millions of aligned viewers via verified creators on CreatorFlow.',
                'body'  => "## Brand awareness campaigns build recall, not conversion\n\nMeasure success in **reach**, **impressions**, and **assisted conversions** — not last-click revenue. Ideal for:\n- **New brand launches** — build recognition from zero\n- **Category expansions** — announce new SKU/collection\n- **Rebrands** — get the new identity in front of your audience\n\n### Structure\n- **20–50 creators** in a single 2-week burst\n- **15–45 second hero video** — repurposable for paid social\n- **Consistent creative direction** — same hook, different creator voices\n- **ER-weighted matching** — CreatorFlow ranks candidates by engagement rate, not follower count\n\n### What CreatorFlow adds\n- **AI brief generator** — one master brief, personalized per creator\n- **Content library** — 20-creator burst = 60+ ad-ready assets\n- **Assisted conversion tracking** — see the multi-touch influence, not just last-click",
                'faqs'  => [
                    ['q' => 'How do I measure brand awareness ROI?', 'a' => 'Reach + impressions + brand-lift study (survey overlay). CreatorFlow shows organic reach in real-time; brand-lift studies are quarterly add-ons for Growth+ plans.'],
                    ['q' => 'How many creators should I book?', 'a' => 'For a launch: 30–50 creators in the same 2-week window creates a "cultural moment". For maintenance: 10–15 per month.'],
                    ['q' => 'Can I whitelist top performers for paid ads?', 'a' => 'Yes. Whitelisting rights are one-click on the assignment page. The creator gets a % share of ad spend.'],
                    ['q' => 'What creator size works best for awareness?', 'a' => 'Multiple micro-creators outperform 1 mega-influencer on ROI. But 1 mega-creator adds cultural weight — mix both.'],
                    ['q' => 'Do you support cross-platform amplification?', 'a' => 'Yes — brief once, repurpose across IG Reels, TikTok, YouTube Shorts. CreatorFlow contracts include cross-platform posting by default.'],
                ],
            ],
            'store-visit' => [
                'title' => 'Store Visit',
                'tagline' => 'Drive real foot traffic',
                'emoji' => '📍', 'grad' => 'from-cyan-500 to-emerald-500',
                'why'   => 'Invite local creators IRL — capture the vibe, drive footfall and win local word of mouth.',
                'bullets' => ['Local micro-creators only', 'Story + Reel combo', 'Attribution via QR / redemption'],
                'meta'  => 'Store visit campaigns: bring local creators into your café, salon, retail store, hotel or restaurant. Real footfall, real content, real attribution.',
                'body'  => "## Store-visit campaigns are the fastest way to drive local awareness\n\nA local creator with 20K followers in your city outperforms a national creator with 500K when your goal is **footfall**. Their audience is your buyer.\n\n### Playbook\n1. **Filter by city** in the creator marketplace\n2. Invite 10 creators for a **complimentary visit** (₹0 cash, product/service value only)\n3. Ask for **Instagram Story series (5-8 stories)** + **1 reel** + **Google review**\n4. Add a **QR code / redemption code** at your billing counter — attributable footfall\n\n### What works\n- **Restaurants** — food shots, ambience, order recommendations\n- **Salons / spas** — before/after experience, staff interaction\n- **Retail** — try-on hauls, staff picks\n- **Boutique hotels** — POV walkthroughs, breakfast, in-room amenities",
                'faqs'  => [
                    ['q' => 'How is store-visit attribution measured?', 'a' => 'QR code at billing, or a unique code redeemed at checkout. CreatorFlow logs each redemption and attributes it to the creator.'],
                    ['q' => 'Do creators travel to my store?', 'a' => 'For local micro-creators, yes — travel is on them. For invited creators from other cities, factor travel + stay into your barter package.'],
                    ['q' => 'What if the creator doesn\'t show up?', 'a' => 'CreatorFlow contracts include a 48-hour cancellation clause. Any no-shows are logged and lower the creator\'s performance score.'],
                    ['q' => 'Can I run store-visit campaigns for multiple locations?', 'a' => 'Yes. Add each location as a separate campaign with location-specific creator filtering. Or run 1 campaign with a "chose your city" flow.'],
                    ['q' => 'What time slot works best for restaurants?', 'a' => 'Weekday lunch or early dinner (5-7pm). Creators prefer natural lighting; you avoid weekend rush.'],
                ],
            ],
            'self-managed' => [
                'title' => 'Self Managed',
                'tagline' => 'You drive, we power the tools',
                'emoji' => '⚡', 'grad' => 'from-indigo-500 to-sky-500',
                'why'   => 'Full access to the platform: creator DB, briefs, contracts, attribution. You run the show.',
                'bullets' => ['Unlimited creator search', 'AI briefs on demand', 'Real-time attribution'],
                'meta'  => 'Self-managed influencer marketing platform: unlimited creator search, AI briefs, contracts, attribution — you drive, CreatorFlow powers it.',
                'body'  => "## For in-house teams that own the strategy\n\nYou know your brand better than any agency. **Self-managed** gives you the CreatorFlow toolkit without the campaign manager.\n\n### What you get\n- **Unlimited creator search** — 100K+ verified creators, 12+ filters\n- **AI brief generator** — one prompt → launch-ready brief\n- **Contract + payment automation** — no legal back-and-forth\n- **Real-time attribution** — codes, referrals, multi-touch\n- **AI content review** — no more manual compliance checks\n\n### Best for\n- **In-house brand teams** with a dedicated influencer marketer\n- **Agencies** managing multiple client brands\n- **DTC founders** running growth themselves in year 0-2\n\n### What you don't get\n- Dedicated campaign manager (upgrade to Managed for that)\n- Custom creative direction / production\n- Weekly reporting calls",
                'faqs'  => [
                    ['q' => 'How many creators can I invite?', 'a' => 'Unlimited on Growth+ plans. Free plan is capped at 5 campaigns/month, still unlimited creators per campaign.'],
                    ['q' => 'Do I need a Shopify store?', 'a' => 'No. CSV, WooCommerce, Amazon, or manual product entry all work. Shopify unlocks live inventory + auto-order sync.'],
                    ['q' => 'Can I upgrade to Managed later?', 'a' => 'Yes — one-click upgrade adds a dedicated campaign manager for a monthly retainer.'],
                    ['q' => 'How steep is the learning curve?', 'a' => 'Most brands ship their first campaign within 24h of signup. AI briefs handle the heavy creative lift.'],
                    ['q' => 'Is there a demo?', 'a' => 'Yes — book a 30-minute demo at contact us. We\'ll walk you through a real campaign.'],
                ],
            ],
            'barter-campaign' => [
                'title' => 'Barter Campaign',
                'tagline' => 'Trade product for content',
                'emoji' => '🎁', 'grad' => 'from-pink-500 to-rose-500',
                'why'   => 'A cost-effective way to generate authentic UGC at scale. 62% average acceptance rate.',
                'bullets' => ['Zero cash to creator', 'Auto Shopify order + tracking', 'Waitlist automation'],
                'meta'  => 'Barter influencer campaigns: trade your product for creator content. Zero cash, 62% acceptance rate, automatic Shopify orders on CreatorFlow.',
                'body'  => "## Barter is the highest-ROI creator format when done right\n\nYou pay only the **cost of goods** (COGS + shipping). Creators get product they genuinely want. The right playbook consistently returns 4-8× on retail-value spend.\n\n### When barter works\n- **Beauty / skincare** — high perceived value, low COGS\n- **Fashion accessories** — jewellery, eyewear, small leather\n- **Food / beverage** — DTC subscription boxes\n- **Home decor** — small, shippable items\n\n### When barter doesn't work\n- Very expensive electronics (₹50K+) — creators expect cash on top\n- Services with high delivery cost\n- Products that don't photograph/video well\n\n### CreatorFlow automates the ops\n- **Auto-Shopify order** with unique tracking\n- **Waitlist** for over-subscribed campaigns\n- **Acceptance rate assumptions** built into invite pool sizing\n- **Contract + usage rights** auto-generated",
                'faqs'  => [
                    ['q' => 'Do creators really accept barter?', 'a' => 'Yes — 62% average acceptance across CreatorFlow. Beauty hits 78%. Fashion 55%. Higher when the product is genuinely desirable and matches the creator\'s niche.'],
                    ['q' => 'How do I set expectations for content?', 'a' => 'The CreatorFlow brief requires: 1 reel or video, 3-5 stories, tag brand + hashtags, use of unique discount code. All in the auto-generated contract.'],
                    ['q' => 'What if a creator ghosts after receiving product?', 'a' => 'Their performance score drops (visible to all future brands). Reputation is real. Ghost rate on CreatorFlow is 4%.'],
                    ['q' => 'Can I do "barter + commission" hybrid?', 'a' => 'Yes — set the campaign type to Hybrid. Product value + % revenue share on referred sales.'],
                    ['q' => 'How many creators can I seed at once?', 'a' => 'Unlimited. Some brands seed 100-500 creators for a single launch. CreatorFlow handles order distribution, waitlists and tracking.'],
                ],
            ],
            'video-shoot' => [
                'title' => 'Product Videoshoot',
                'tagline' => 'Studio-grade UGC for paid ads',
                'emoji' => '🎬', 'grad' => 'from-emerald-500 to-teal-500',
                'why'   => 'High-quality creator-produced video ready for paid social, DTC pages and marketplaces.',
                'bullets' => ['Multi-angle deliverables', 'Ad-format exports (9:16, 1:1, 16:9)', 'Full usage rights'],
                'meta'  => 'Get studio-quality UGC video from verified creators — ready for paid ads, DTC pages and marketplaces. Multi-format exports, full usage rights.',
                'body'  => "## When you need ads, not organic content\n\nOrganic barter/seeding is great for content **volume**. But paid-social requires **quality**: cleaner audio, better cuts, deliberate hooks. That's what our video-shoot campaigns produce.\n\n### What you get\n- **3-5 videos** per creator, each in **3 aspect ratios** (9:16 for Reels/TikTok, 1:1 for feed, 16:9 for YouTube pre-roll)\n- **Raw footage** + finished cuts\n- **Full usage rights** — perpetual, paid + organic, all platforms\n- **Scripted hook variations** for split-testing on Meta / TikTok Ads\n\n### Pricing\nStarts at **₹40,000 per creator** for 3-video packages. Bulk discounts for 10+ creators.\n\n### Turnaround\nAround 14 days from creator approval to final delivery.",
                'faqs'  => [
                    ['q' => 'How is this different from barter?', 'a' => 'Barter gets you organic-style content (creator\'s aesthetic). Videoshoot gets you ad-ready content (your creative direction, creator\'s face). Different use cases.'],
                    ['q' => 'Do I own the footage?', 'a' => 'Yes — full usage rights, perpetual, all platforms, paid and organic. Included in the campaign contract.'],
                    ['q' => 'Can I approve multiple hook variations?', 'a' => 'Yes — you can specify up to 3 hook variations per video. Creators shoot all three, you split-test on Meta / TikTok Ads.'],
                    ['q' => 'What creator type works for shoots?', 'a' => 'Semi-pro creators who\'ve done paid partnerships before. Filter the marketplace by "Verified" + "Available for paid".'],
                    ['q' => 'Do you handle post-production?', 'a' => 'Creators deliver edited videos. If you need additional cuts / animation, add a video editor from CreatorFlow\'s partner network.'],
                ],
            ],
        ];
    }

    public function tools()             { return view('marketing.tools.index'); }
    public function toolRoiCalculator() { return view('marketing.tools.roi-calculator'); }
    public function toolRateCalculator(){ return view('marketing.tools.rate-calculator'); }
    public function toolBriefGenerator(){ return view('marketing.tools.brief-generator'); }

    public function resources()  { return view('marketing.resources.index'); }

    public function blogIndex()
    {
        // If any DB-managed posts exist, prefer them. Otherwise fall back to the
        // seeded demo list so the site still looks alive after a fresh install.
        $dbPosts = BlogPost::published()->latest('published_at')->latest()->get();
        $posts = $dbPosts->isNotEmpty() ? $dbPosts->map(fn ($p) => $this->normalize($p))->all() : $this->demoPosts();

        return view('marketing.blog.index', ['posts' => $posts]);
    }

    public function blogShow(string $slug)
    {
        $db = BlogPost::published()->where('slug', $slug)->first();
        if ($db) {
            $related = BlogPost::published()->where('id', '!=', $db->id)->latest()->take(3)->get()
                ->map(fn ($p) => $this->normalize($p))->all();
            return view('marketing.blog.show', [
                'post' => $this->normalize($db, withBody: true),
                'related' => $related,
                'faqJson' => $db->faq_json,
            ]);
        }

        $posts = $this->demoPosts();
        $post = collect($posts)->firstWhere('slug', $slug);
        abort_unless($post, 404);
        return view('marketing.blog.show', [
            'post'    => $post,
            'related' => collect($posts)->where('slug', '!=', $slug)->take(3)->values(),
            'faqJson' => null,
        ]);
    }

    public function sitemap()
    {
        $urls = collect([
            ['loc' => url('/'),                       'priority' => '1.0'],
            ['loc' => route('features'),              'priority' => '0.8'],
            ['loc' => route('pricing'),               'priority' => '0.8'],
            ['loc' => route('about'),                 'priority' => '0.6'],
            ['loc' => route('contact'),               'priority' => '0.6'],
            ['loc' => route('tools.index'),           'priority' => '0.7'],
            ['loc' => route('tools.roi'),             'priority' => '0.6'],
            ['loc' => route('tools.rate'),            'priority' => '0.6'],
            ['loc' => route('tools.brief'),           'priority' => '0.6'],
            ['loc' => route('resources'),             'priority' => '0.6'],
            ['loc' => route('blog.index'),            'priority' => '0.8'],
            ['loc' => route('register'),              'priority' => '0.5'],
        ]);

        foreach (array_keys(static::industryData()) as $slug) {
            $urls->push(['loc' => route('industry.show', $slug), 'priority' => '0.7']);
        }
        foreach (array_keys(static::campaignTypeData()) as $slug) {
            $urls->push(['loc' => route('campaign-type.show', $slug), 'priority' => '0.7']);
        }

        $urls = $urls->concat(
            BlogPost::published()->get()->map(fn ($p) => [
                'loc' => route('blog.show', $p->slug),
                'lastmod' => optional($p->updated_at)->toAtomString(),
                'priority' => '0.7',
            ])
        );

        $xml = '<?xml version="1.0" encoding="UTF-8"?>'.
            '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
        foreach ($urls as $u) {
            $xml .= '<url><loc>'.$u['loc'].'</loc>';
            if (! empty($u['lastmod'])) $xml .= '<lastmod>'.$u['lastmod'].'</lastmod>';
            $xml .= '<priority>'.$u['priority'].'</priority></url>';
        }
        $xml .= '</urlset>';

        return Response::make($xml, 200, ['Content-Type' => 'application/xml']);
    }

    public function robots()
    {
        $txt = "User-agent: *\nAllow: /\nDisallow: /brand/\nDisallow: /creator/\nDisallow: /admin/\nDisallow: /messages/\nSitemap: ".url('/sitemap.xml')."\n";
        return Response::make($txt, 200, ['Content-Type' => 'text/plain']);
    }

    protected function normalize(BlogPost $p, bool $withBody = false): array
    {
        return [
            'slug'      => $p->slug,
            'title'     => $p->title,
            'excerpt'   => $p->excerpt ?: str($p->body)->stripTags()->limit(160),
            'date'      => optional($p->published_at ?? $p->created_at)->toDateString(),
            'read'      => $p->read_minutes ?: '5 min',
            'category'  => $p->category ?: 'Article',
            'author'    => $p->author?->name ?? 'CreatorFlow team',
            'grad'      => $p->cover_gradient ?: 'from-violet-500 to-pink-500',
            'cover_image_path' => $p->cover_image_path,
            'meta_title'       => $p->metaTitle(),
            'meta_description' => (string) $p->metaDescription(),
            'canonical_url'    => $p->canonical_url,
            'body'      => $withBody ? $p->body : null,
            'faq_json'  => $p->faq_json,
        ];
    }

    protected function demoPosts(): array
    {
        return [
            [
                'slug' => 'seeding-vs-paid-ugc-2025',
                'title' => 'Seeding vs. paid UGC in 2025: which one actually converts?',
                'excerpt' => 'A data-backed breakdown of when barter beats paid, when it doesn\'t, and how to blend the two for maximum ROAS.',
                'date' => '2025-06-18', 'read' => '7 min', 'category' => 'Playbooks',
                'author' => 'Priya Sharma', 'grad' => 'from-violet-500 to-pink-500',
            ],
            [
                'slug' => 'first-100-creators-checklist',
                'title' => 'The first 100 creators: a launch checklist for DTC founders',
                'excerpt' => 'Everything from finding hero products to writing the invite DM, with templates you can copy today.',
                'date' => '2025-05-30', 'read' => '9 min', 'category' => 'Guides',
                'author' => 'Marcus Cole', 'grad' => 'from-cyan-500 to-emerald-500',
            ],
            [
                'slug' => 'ai-briefs-that-dont-sound-ai',
                'title' => 'AI campaign briefs that don\'t sound like AI',
                'excerpt' => 'Five prompt patterns we use inside CreatorFlow to generate briefs creators actually love executing.',
                'date' => '2025-05-11', 'read' => '6 min', 'category' => 'Tactics',
                'author' => 'Aria Kim', 'grad' => 'from-amber-500 to-rose-500',
            ],
            [
                'slug' => 'shopify-attribution-fixed',
                'title' => 'Fixing creator attribution on Shopify (finally)',
                'excerpt' => 'How unique codes + referral links + multi-touch tracking come together to tie creators to actual revenue.',
                'date' => '2025-04-22', 'read' => '11 min', 'category' => 'Engineering',
                'author' => 'Devon Patel', 'grad' => 'from-indigo-500 to-violet-500',
            ],
            [
                'slug' => 'creator-rate-cards-benchmarks',
                'title' => 'Creator rate card benchmarks by niche &amp; follower count',
                'excerpt' => 'What UGC, Reels, and full videos cost in 2025 — a benchmark from 12,000+ CreatorFlow deals.',
                'date' => '2025-04-05', 'read' => '8 min', 'category' => 'Benchmarks',
                'author' => 'Nova Chen', 'grad' => 'from-emerald-500 to-teal-500',
            ],
            [
                'slug' => 'gen-z-creator-brief-template',
                'title' => 'The Gen‑Z creator brief template we swear by',
                'excerpt' => 'Short, punchy, non‑corporate — and it works. Copy the exact template we use for viral drops.',
                'date' => '2025-03-19', 'read' => '5 min', 'category' => 'Templates',
                'author' => 'Zia Park', 'grad' => 'from-pink-500 to-fuchsia-500',
            ],
        ];
    }
}
