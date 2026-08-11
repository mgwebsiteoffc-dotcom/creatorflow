<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use App\Support\SeoData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class MarketingController extends Controller
{
    public function features()  { return view('marketing.features'); }
    public function pricing()   { return view('marketing.pricing'); }
    public function about()     { return view('marketing.about'); }
    public function contact()   { return view('marketing.contact'); }

    public function tools()             { return view('marketing.tools.index'); }
    public function toolRoiCalculator() { return view('marketing.tools.roi-calculator'); }
    public function toolRateCalculator(){ return view('marketing.tools.rate-calculator'); }
    public function toolBriefGenerator(){ return view('marketing.tools.brief-generator'); }

    /**
     * POST /tools/brief-generator/ai — returns a JSON body { brief: "..." }.
     * Uses the admin-configured AI provider (OpenAI or offline mock).
     */
    public function generateBriefApi(\Illuminate\Http\Request $request, \App\Domains\AI\AiGateway $ai)
    {
        $data = $request->validate([
            'product_name' => ['required', 'string', 'max:200'],
            'description'  => ['nullable', 'string', 'max:2000'],
            'niche'        => ['nullable', 'string', 'max:80'],
            'format'       => ['nullable', 'string', 'max:80'],
            'tone'         => ['nullable', 'string', 'max:40'],
        ]);

        $prompt = sprintf(
            "Write a launch-ready influencer campaign brief in plain text (no markdown).\n"
            ."Brand product: %s\nWhat it does: %s\nNiche: %s\nFormat: %s\nTone: %s\n"
            ."Structure the brief with clear sections separated by '━━━':\n"
            ."• THE PRODUCT (2 sentences)\n"
            ."• WHY IT MATTERS (2 sentences that motivate the creator)\n"
            ."• HOOK OPTIONS (3 punchy options in the requested tone)\n"
            ."• STORYBOARD (5 shots numbered 1–5)\n"
            ."• MUST INCLUDE (5 bullets)\n"
            ."• DO / DON'T (3 do, 3 don't)\n"
            ."• DELIVERABLES (format, aspect, deadline placeholder, 90-day usage rights)\n"
            ."• COMPENSATION (as agreed in CreatorFlow contract, +25%% bonus for videos >5%% ER)\n"
            ."Prefix the whole brief with 'CAMPAIGN BRIEF · %s' and today's date.\n"
            ."Tune tone and references for the Indian creator marketplace (Delhi / Mumbai / Bangalore Reels + Shorts audience).",
            $data['product_name'],
            $data['description'] ?? '(not provided)',
            $data['niche'] ?? 'Lifestyle',
            $data['format'] ?? 'Instagram Reel',
            $data['tone'] ?? 'Authentic',
            $data['product_name'],
        );

        try {
            $response = $ai->complete(
                task: 'generate_brief',
                messages: [
                    ['role' => 'system', 'content' => 'You are a senior creator-marketing strategist for CreatorFlow, an India-first influencer marketing platform. Write clear, useful, non-generic campaign briefs.'],
                    ['role' => 'user',   'content' => $prompt],
                ],
                options: ['temperature' => (float) (\App\Models\PlatformSetting::current()->ai_temperature ?? 0.4), 'seed' => [
                    'product_title' => $data['product_name'],
                ]],
            );

            return response()->json([
                'brief'  => trim($response->text),
                'model'  => $response->model,
                'source' => config('creatorflow.ai.driver') === 'openai' ? 'openai' : 'mock',
            ]);
        } catch (\Throwable $e) {
            // Return graceful failure so the frontend can drop to its offline template.
            return response()->json([
                'error' => 'AI service unavailable',
                'hint'  => 'Client will fall back to the offline template.',
            ], 503);
        }
    }

    public function resources()
    {
        return view('marketing.resources.index', [
            'categories' => static::resourceCategories(),
            'items'      => static::resourcesData(),
        ]);
    }

    public function resourceCategory(string $category)
    {
        $meta = static::resourceCategories()[$category] ?? abort(404);
        $items = collect(static::resourcesData())->where('category', $category)->values();
        return view('marketing.resources.category', compact('category', 'meta', 'items'));
    }

    public function resourceShow(string $slug)
    {
        $item = collect(static::resourcesData())->firstWhere('slug', $slug) ?? abort(404);
        return view('marketing.resources.show', compact('item'));
    }

    /**
     * Content library — each item is a real, on-page article/download. We ship
     * these as static content so /resources isn't a page of dead links.
     */
    public static function resourceCategories(): array
    {
        return [
            'playbooks'  => ['label' => 'Playbooks',  'icon' => '📘', 'grad' => 'from-violet-500 to-pink-500',   'sub' => 'Step-by-step guides to run better campaigns.'],
            'benchmarks' => ['label' => 'Benchmarks', 'icon' => '📊', 'grad' => 'from-cyan-500 to-emerald-500',  'sub' => 'India-first rate + ROI benchmarks, updated 2026.'],
            'templates'  => ['label' => 'Templates',  'icon' => '🧾', 'grad' => 'from-amber-500 to-rose-500',    'sub' => 'Briefs, contracts, spreadsheets — copy + edit.'],
            'videos'     => ['label' => 'Videos',     'icon' => '🎥', 'grad' => 'from-indigo-500 to-violet-500', 'sub' => 'Product tours and micro-lessons under 15 min.'],
        ];
    }

    public static function resourcesData(): array
    {
        return [
            [
                'slug' => 'dtc-seeding-playbook',
                'category' => 'playbooks',
                'title' => 'The DTC seeding playbook for Indian brands',
                'summary' => 'How to seed 100 creators in 30 days without spending on fees. Cover story: Glow &amp; Co., Delhi.',
                'read_min' => 12,
                'body' => "## Why seeding beats paid for launch weeks\nBarter (product-only) campaigns average 62% acceptance in India when the offer is clear, the product is desirable, and the brief is tight. That's 4× the acceptance rate of cold paid outreach.\n\n## The 5-step playbook\n1. **Shortlist** 300 creators using CreatorFlow's marketplace filtered by city + tier + niche.\n2. **Rank** them by engagement rate (min 4%) and audience overlap.\n3. **Personalise** the outreach — reference their recent post.\n4. **Ship** the product within 3 business days (see our Shipping Policy).\n5. **Ask** for a Reel + Story + 1 permission for whitelisting.\n\n## Delhi micro-influencer campaign math\nWith 100 seeded creators × 32K avg followers × 6% ER × 1.4% CVR × ₹1,299 AOV, expected revenue = ₹3.5L on a ~₹1.2L product-cost outlay. That's a 2.9× ROAS on retail — before whitelisting.\n",
            ],
            [
                'slug' => 'india-creator-rate-benchmarks-2026',
                'category' => 'benchmarks',
                'title' => 'India creator rate benchmarks 2026 (₹)',
                'summary' => 'Every rate benchmark from 12,000+ CreatorFlow deals across Delhi, Mumbai, Bangalore.',
                'read_min' => 6,
                'body' => "## Nano (1K–10K)\nUGC: ₹0–₹2,500 · Reel: ₹1,500–₹6,000 · YouTube 60s: ₹3,000–₹10,000\n\n## Micro (10K–100K)\nUGC: ₹1,500–₹8,000 · Reel: ₹5,000–₹40,000 · YouTube 60s: ₹15,000–₹90,000\n\n## Mid (100K–500K)\nUGC: ₹6,000–₹35,000 · Reel: ₹30,000–₹1,50,000 · YouTube 60s: ₹80,000–₹4,00,000\n\n## Macro (500K–1M)\nUGC: ₹25,000–₹80,000 · Reel: ₹1,00,000–₹4,00,000 · YouTube 60s: ₹3,00,000–₹10,00,000\n\n## Mega (1M+)\nUGC: ₹60,000+ · Reel: ₹3,00,000+ · YouTube 60s: ₹8,00,000+\n\nUse the [rate calculator](/tools/creator-rate-calculator) with the actual creator's ER + niche for a live number.",
            ],
            [
                'slug' => 'attribution-101',
                'category' => 'playbooks',
                'title' => 'Attribution 101 — tie every ₹ back to the creator',
                'summary' => 'Discount codes + UTM + referral links + assisted conversions in one flow.',
                'read_min' => 8,
                'body' => "## Every creator gets 3 handles\n1. A **unique discount code** (e.g. RIYA10)\n2. A **UTM-tagged referral link** synced from your Shopify store\n3. A **short.link** so the tracking survives copy-paste to Reels captions\n\n## Rolling up assisted conversions\nCreatorFlow's multi-touch attribution assigns full credit to the last-touch code + 30% assist credit to any earlier touch. Set the attribution window (7–30 days) in `Admin → Settings`.\n",
            ],
            [
                'slug' => 'brief-template-pack',
                'category' => 'templates',
                'title' => 'Brief template pack (10 briefs)',
                'summary' => 'Copy-and-edit briefs for every campaign type — product review, launch week, barter, whitelisting.',
                'read_min' => 3,
                'body' => "Use these as a starting point — every one plugs into the AI brief generator to be reshaped in seconds.\n\n1. Beauty · Barter · Reel\n2. Beauty · Paid · Reel + Story\n3. Fashion · Try-on haul · Reel\n4. Food · Recipe · Reel\n5. Home · Room reset · Reel\n6. Tech · Unboxing · YouTube Short\n7. Fitness · Progress · Reel\n8. Travel · Stay review · Reel + carousel\n9. Wellness · Morning routine · Reel\n10. Kids · Product demo · Reel\n\nFire up the [AI brief generator](/tools/brief-generator) and pick one to hydrate with your product.",
            ],
            [
                'slug' => 'barter-mastery-course',
                'category' => 'playbooks',
                'title' => 'Barter mastery — free 6-part guide',
                'summary' => 'The full case study of how a Delhi skincare brand seeded 240 creators in 60 days on product cost alone.',
                'read_min' => 15,
                'body' => "### Part 1 · Offer design\nMake the barter offer feel worth ₹5,000 even if COGS is ₹800.\n\n### Part 2 · Creator sourcing\nCity + tier + niche + audience overlap filter → 500-creator shortlist.\n\n### Part 3 · The 4-sentence pitch\nHook · gift · ask · CTA.\n\n### Part 4 · Contract + rights\n90-day paid usage on Reel + Story is the sweet spot.\n\n### Part 5 · Content review\nAI review catches missing #ad + weak hook in <2 seconds.\n\n### Part 6 · Whitelisting\nTurn top 20% of seeded creators into paid-ad creative.\n",
            ],
            [
                'slug' => 'product-tour-video',
                'category' => 'videos',
                'title' => '15-min product tour',
                'summary' => 'Everything CreatorFlow does, in the time it takes to make chai.',
                'read_min' => 15,
                'body' => "Watch the founder walk through a full campaign — marketplace search, campaign create, audience targeting, invitation, contract, content review, attribution.\n\nBook a 1:1 walkthrough via the [contact page](/contact).",
            ],
        ];
    }

    public function blogIndex()
    {
        $dbPosts = \App\Support\SchemaCheck::has('blog_posts')
            ? BlogPost::published()->latest('published_at')->latest()->get()
            : collect();
        $posts = $dbPosts->isNotEmpty() ? $dbPosts->map(fn ($p) => $this->normalize($p))->all() : $this->demoPosts();
        return view('marketing.blog.index', ['posts' => $posts]);
    }

    public function blogShow(string $slug)
    {
        $db = \App\Support\SchemaCheck::has('blog_posts')
            ? BlogPost::published()->where('slug', $slug)->first()
            : null;
        if ($db) {
            $related = BlogPost::published()->where('id', '!=', $db->id)->latest()->take(3)->get()
                ->map(fn ($p) => $this->normalize($p))->all();
            return view('marketing.blog.show', [
                'post'    => $this->normalize($db, withBody: true),
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
            ['loc' => route('resources.category', 'playbooks'),  'priority' => '0.55'],
            ['loc' => route('resources.category', 'benchmarks'), 'priority' => '0.55'],
            ['loc' => route('resources.category', 'templates'),  'priority' => '0.55'],
            ['loc' => route('resources.category', 'videos'),     'priority' => '0.55'],
            ['loc' => route('legal.terms'),           'priority' => '0.4'],
            ['loc' => route('legal.privacy'),         'priority' => '0.4'],
            ['loc' => route('legal.refund'),          'priority' => '0.4'],
            ['loc' => route('legal.cookies'),         'priority' => '0.3'],
            ['loc' => route('legal.shipping'),        'priority' => '0.3'],
            ['loc' => route('legal.content'),         'priority' => '0.3'],
            ['loc' => route('legal.creator-agreement'), 'priority' => '0.3'],
            ['loc' => route('blog.index'),            'priority' => '0.8'],
            ['loc' => route('register'),              'priority' => '0.5'],
        ]);

        foreach (array_keys(static::industryData()) as $slug) {
            $urls->push(['loc' => route('industry.show', $slug), 'priority' => '0.7']);
        }
        foreach (static::resourcesData() as $r) {
            $urls->push(['loc' => route('resources.show', $r['slug']), 'priority' => '0.6']);
        }
        foreach (array_keys(static::campaignTypeData()) as $slug) {
            $urls->push(['loc' => route('campaign-type.show', $slug), 'priority' => '0.7']);
        }

        // Programmatic service + city landing pages
        $urls->push(['loc' => route('services.index'), 'priority' => '0.8']);
        foreach (array_keys(SeoData::services()) as $serviceSlug) {
            $urls->push(['loc' => route('services.show', $serviceSlug), 'priority' => '0.8']);
            foreach (array_keys(SeoData::cities()) as $citySlug) {
                $urls->push(['loc' => route('services.city', [$serviceSlug, $citySlug]), 'priority' => '0.7']);
            }
        }

        if (\App\Support\SchemaCheck::has('blog_posts')) {
            $urls = $urls->concat(
                BlogPost::published()->get()->map(fn ($p) => [
                    'loc' => route('blog.show', $p->slug),
                    'lastmod' => optional($p->updated_at)->toAtomString(),
                    'priority' => '0.7',
                ])
            );
        }

        $xml = '<?xml version="1.0" encoding="UTF-8"?><urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
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

    /**
     * llms.txt — the emerging convention for feeding AI answer engines
     * (ChatGPT / Perplexity / Gemini) a clean summary of what to cite.
     * https://llmstxt.org
     */
    public function llmsTxt()
    {
        $lines = [
            '# CreatorFlow',
            '',
            '> The AI-powered influencer marketing platform for DTC brands, Shopify stores and agencies in India and globally.',
            '',
            '## Company',
            '- Name: CreatorFlow',
            '- What we do: Influencer marketing agency + software platform',
            '- Location: India (Delhi NCR HQ, remote-first)',
            '- Categories: Influencer marketing, UGC, creator seeding, barter campaigns, Shopify integration',
            '- Pricing: Free forever plan, paid tiers from ₹0/mo',
            '- URL: '.url('/'),
            '',
            '## Key pages',
            '- Homepage: '.url('/'),
            '- Features: '.route('features'),
            '- Pricing: '.route('pricing'),
            '- Services: '.route('services.index'),
            '- Blog: '.route('blog.index'),
            '- Contact: '.route('contact'),
            '',
            '## Services (with city variants)',
        ];

        foreach (SeoData::services() as $slug => $svc) {
            $lines[] = "- **{$svc['name']}** — {$svc['tagline']} → ".route('services.show', $slug);
        }

        $lines[] = '';
        $lines[] = '## Cities we serve';
        foreach (SeoData::cities() as $slug => $city) {
            $lines[] = "- {$city['name']} ({$city['region']}) — {$city['note']}";
        }

        $lines[] = '';
        $lines[] = '## Highest-intent city + service combinations';
        foreach (['delhi','mumbai','bangalore'] as $city) {
            foreach (['influencer-marketing-agency','ugc-influencers','barter-influencers'] as $service) {
                $lines[] = "- ".route('services.city', [$service, $city]);
            }
        }

        $lines[] = '';
        $lines[] = '## Common questions we answer';
        $lines[] = "- What does an influencer marketing agency in Delhi cost?";
        $lines[] = "- How does barter influencer marketing work?";
        $lines[] = "- Which platform is best for UGC video for Meta Ads?";
        $lines[] = "- How do I attribute sales to a specific creator on Shopify?";
        $lines[] = "- How many creators should I seed for a launch?";

        return Response::make(implode("\n", $lines)."\n", 200, ['Content-Type' => 'text/plain; charset=utf-8']);
    }

    /**
     * Rich, structured data for each industry landing page.
     * The template treats every array key as an optional section — omit
     * a key and that section is skipped.
     */
    public static function industryData(): array
    {
        return [
            'fashion-and-lifestyle' => [
                'title'   => 'Fashion & Lifestyle',
                'tagline' => 'Aesthetic drops, real ER, viral hauls.',
                'emoji'   => '👗',
                'grad'    => 'from-fuchsia-500 to-pink-500',
                'accent'  => '#ec4899',
                'meta'    => 'Run fashion & lifestyle influencer campaigns on CreatorFlow — AI-matched creators, bulk seeding, live attribution.',
                'hero_kpis' => [['6.8%', 'Avg engagement'], ['5.4×', 'Avg ROAS'], ['Reels', 'Best format']],
                'why_headline' => 'Why creators outperform ads for fashion',
                'why_body'     => 'Fashion is an emotion buy. Buyers convert when a creator they trust wears the piece in a real setting. CreatorFlow powers the loop: match, seed, ship, review, attribute.',
                'why_points'   => [
                    ['icon'=>'👥', 'title'=>'Trust wins',   'body'=>'Buyers trust creators over paid ads 3.2× in apparel.'],
                    ['icon'=>'🎬', 'title'=>'Reels convert','body'=>'6.8% avg ER on Reels — 2.4× your best paid ad CTR.'],
                    ['icon'=>'💸', 'title'=>'Lower CAC',   'body'=>'Barter + 5% commission beats pure paid by 3.1× ROAS.'],
                ],
                'playbook' => [
                    ['step'=>1, 'title'=>'Pick one hero SKU',       'body'=>'Single silhouette, 2-3 colorways. Focus your creative direction.'],
                    ['step'=>2, 'title'=>'Match by audience overlap','body'=>'Filter creators whose audience matches ≥ 40% of your buyer persona.'],
                    ['step'=>3, 'title'=>'Seed 30 across 3 tiers',   'body'=>'10 nano · 15 micro · 5 mid — content volume + reach.'],
                    ['step'=>4, 'title'=>'Ship + attribute',         'body'=>'Auto-Shopify orders, unique codes, real-time revenue tracking.'],
                ],
                'brands'   => ['Roving Mode','Evereve','Fable Street','Nykaa Fashion','Bewakoof','Sanchi','Fabindia','Nicobar'],
                'examples' => [
                    ['name'=>'Roving Mode', 'result'=>'40 seeded creators → 60 UGC assets → ₹4.2L attributed revenue in 30 days', 'grad'=>'from-fuchsia-500 to-pink-500'],
                    ['name'=>'Evereve',     'result'=>'Barter-only campaign, 780K reach, 12% CVR on new-visitor traffic',        'grad'=>'from-rose-500 to-orange-500'],
                ],
                'testimonial' => ['q'=>'We stopped booking creators by follower count and started booking by audience overlap. Our CPA dropped 42% in six weeks.', 'name'=>'Growth Lead', 'company'=>'DTC fashion brand'],
                'faqs'  => [
                    ['q'=>'How much does a fashion influencer campaign cost in India?','a'=>'Barter (product-only) campaigns cost you just the retail value of the seeded items. Paid campaigns range from ₹5,000 for nano-creators to ₹80,000+ for macro fashion creators.'],
                    ['q'=>'Do creators need to have a Shopify account?','a'=>'No. Creators sign up free on CreatorFlow. When a brand approves them, we generate a unique discount code and (for Shopify brands) create the order with tracking automatically.'],
                    ['q'=>'How is fashion campaign performance measured?','a'=>'Every creator gets a unique code + referral link. Attributed revenue, orders, CVR, ER and ROAS show in real-time on your campaign page.'],
                    ['q'=>'Can I run barter-only fashion campaigns?','a'=>'Yes. 68% of fashion campaigns on CreatorFlow are barter-only. Set the campaign type to Barter and the creator fee to 0.'],
                    ['q'=>'What product types work best?','a'=>'Wear-first (tees, dresses, activewear) convert fastest. Accessories (jewellery, eyewear) work great for micro-creator seeding thanks to lower per-unit cost.'],
                ],
            ],

            'beauty-and-cosmetics' => [
                'title'   => 'Beauty & Cosmetics',
                'tagline' => 'Skin-first content, honest reviews, quick trials.',
                'emoji'   => '💄',
                'grad'    => 'from-rose-500 to-orange-500',
                'accent'  => '#f43f5e',
                'meta'    => 'Grow your beauty brand with creator-led UGC. Seed 100s of creators, review content, attribute sales.',
                'hero_kpis' => [['8.1%', 'Avg engagement'], ['7.2×', 'Avg ROAS'], ['62%', 'Accept rate']],
                'why_headline' => 'Beauty runs on trust — creators are the trust layer',
                'why_body'     => 'Buyers scroll for honest reactions: swatches, before/afters, GRWM reels. The shelf you can\'t buy: authentic content that shows results.',
                'why_points'   => [
                    ['icon'=>'🎯','title'=>'#1 seeding category','body'=>'62% average acceptance rate — creators want beauty products.'],
                    ['icon'=>'💥','title'=>'Highest ER of any niche','body'=>'8.1% average engagement on beauty content on CreatorFlow.'],
                    ['icon'=>'🖼️','title'=>'Ad-ready UGC library','body'=>'One 30-creator seed = 90+ assets you can whitelist for paid.'],
                ],
                'playbook' => [
                    ['step'=>1,'title'=>'Launch week',   'body'=>'Seed 50 nano-creators for max volume + awareness.'],
                    ['step'=>2,'title'=>'Weeks 2–4',     'body'=>'Paid campaigns with top 5 performers, whitelist for ads.'],
                    ['step'=>3,'title'=>'Month 2',       'body'=>'Expand into review + tutorial content.'],
                    ['step'=>4,'title'=>'Ongoing',       'body'=>'Quarterly barter drops for every new SKU.'],
                ],
                'brands'   => ['Glow & Co.','Sugar','Plum','Mamaearth','Wow Skin','Foxtale','Dot & Key','Minimalist'],
                'examples' => [
                    ['name'=>'Glow & Co.','result'=>'50-creator launch, ₹8.4L attributed revenue, 220 UGC assets in 2 weeks','grad'=>'from-rose-500 to-orange-500'],
                    ['name'=>'Foxtale',   'result'=>'Barter → paid whitelist funnel, 7.2× ROAS quarter-over-quarter',           'grad'=>'from-pink-500 to-fuchsia-500'],
                ],
                'testimonial' => ['q'=>'CreatorFlow ran our barter seeding end-to-end. UGC quality was so good we\'re now using it in our paid ads.','name'=>'Head of Growth','company'=>'DTC skincare brand'],
                'faqs'  => [
                    ['q'=>'Do beauty creators disclose that content is sponsored?','a'=>'Yes. CreatorFlow contracts include #ad / #paidpartnership requirements per platform. AI content review flags missing disclosure.'],
                    ['q'=>'Which beauty products work best for seeding?','a'=>'Everyday-use SKUs with visible results: serums, lipsticks, mascaras, foundations. Fragrance seeding works but needs better creative direction.'],
                    ['q'=>'How do I run FDA / regulatory-compliant campaigns?','a'=>'Add compliance rules to the brief (no medical claims, no comparisons by name). AI content review flags violations before content ships.'],
                    ['q'=>'Can I invite verified beauty influencers only?','a'=>'Yes — filter the creator marketplace by verified + niche = Beauty & Skincare.'],
                    ['q'=>'What\'s the typical timeline?','a'=>'Beauty campaigns are the fastest cycle — 4–7 days from launch to first content live.'],
                ],
            ],

            'food-and-fitness' => [
                'title'   => 'Food & Fitness',
                'tagline' => 'Recipes, workouts, sampling that converts.',
                'emoji'   => '🥗',
                'grad'    => 'from-emerald-500 to-teal-500',
                'accent'  => '#10b981',
                'meta'    => 'Run food, beverage, wellness and fitness influencer campaigns with CreatorFlow.',
                'hero_kpis' => [['9.4%','Avg engagement'], ['4.6×','Avg ROAS'], ['3.2×','Repeat rate']],
                'why_headline' => 'Food + fitness convert on demonstration',
                'why_body'     => 'Unlike fashion (aesthetic) or beauty (trust), food and fitness convert on proof. Show it working, show the recipe, show the transformation.',
                'why_points'   => [
                    ['icon'=>'🍳','title'=>'Recipe reels','body'=>'The highest-performing format for packaged food + sauces.'],
                    ['icon'=>'🏋️','title'=>'Transformation content','body'=>'60-day series unlock high-intent fitness cohorts.'],
                    ['icon'=>'🔁','title'=>'Repeat = LTV','body'=>'Food/wellness sees 3.2× repeat rate within 90 days.'],
                ],
                'playbook' => [
                    ['step'=>1,'title'=>'7-day supply','body'=>'Enough for the creator to see + show a result.'],
                    ['step'=>2,'title'=>'Ask for process','body'=>'Content > end-state photo. Show the making.'],
                    ['step'=>3,'title'=>'Day-30 whitelisting','body'=>'Convert top-performers to paid.'],
                    ['step'=>4,'title'=>'Subscription discount','body'=>'Unlock LTV via creator codes.'],
                ],
                'brands'   => ['Samsara Ghee','Nourish You','Wellbeing','Slurrp Farm','Farmley','The Whole Truth','Blue Tribe','Yoga Bar'],
                'examples' => [
                    ['name'=>'Samsara Ghee','result'=>'Recipe reels x 40 creators → 3.4M reach, ₹6.1L revenue, 2.8× LTV','grad'=>'from-amber-500 to-orange-500'],
                    ['name'=>'Nourish You','result'=>'Fitness creator series → 12 subscription conversions per creator','grad'=>'from-emerald-500 to-teal-500'],
                ],
                'testimonial' => ['q'=>'Recipe content from 20 seeded creators became our top-performing paid ads for 6 months straight.','name'=>'CMO','company'=>'DTC food brand'],
                'faqs' => [
                    ['q'=>'Do you support restaurant and cafe store-visit campaigns?','a'=>'Yes. Use the Store visit campaign type — filter creators by city, invite them, they film in-store, you get local reach and footfall.'],
                    ['q'=>'What about supplement compliance?','a'=>'Same as beauty — add compliance rules to your brief (no medical claims, no false transformations). AI review enforces them.'],
                    ['q'=>'Can creators show cooking with the product?','a'=>'Yes — recipe reels are the highest-performing content type for food. Provide a recipe brief; creators shoot the process.'],
                    ['q'=>'Do fitness creators need proof of results?','a'=>'For long-form testimonials, yes. For general placement (apparel, gear), no — just clear brand tags and honest usage.'],
                    ['q'=>'What\'s best for beverage brands?','a'=>'Barter seeding at scale (50+ creators). Low per-unit cost, high content volume, inherently shareable.'],
                ],
            ],

            'tech-and-education' => [
                'title'   => 'Tech & Education',
                'tagline' => 'Long-form trust, demo-first video.',
                'emoji'   => '💻',
                'grad'    => 'from-cyan-500 to-blue-500',
                'accent'  => '#0ea5e9',
                'meta'    => 'Tech and EdTech brands: run creator campaigns that convert with long-form YouTube + tutorial reels.',
                'hero_kpis' => [['4.2%','Avg engagement'], ['9.1×','Avg ROAS'], ['YouTube','Best format']],
                'why_headline' => 'Tech buyers want proof, not promises',
                'why_body'     => 'Software, gadgets, and EdTech buyers spend hours in research. Your creator strategy needs long-form YouTube integrations or detailed tutorial reels — not lifestyle drops.',
                'why_points'   => [
                    ['icon'=>'▶','title'=>'YouTube integration','body'=>'60-sec segment inside a review — 9.1× ROAS on avg.'],
                    ['icon'=>'📝','title'=>'Twitter threads','body'=>'Screenshots + use cases for developer tools.'],
                    ['icon'=>'🎓','title'=>'EdTech hooks','body'=>'3-second promise of a concrete outcome converts best.'],
                ],
                'playbook' => [
                    ['step'=>1,'title'=>'Match by audience intent','body'=>'Tech-curious lifestyle creators often out-convert pure tech reviewers.'],
                    ['step'=>2,'title'=>'Demo-first briefs','body'=>'AI generates hook + demo cadence per creator.'],
                    ['step'=>3,'title'=>'Cross-platform posting','body'=>'One integration → clip for Reels, Shorts, TikTok.'],
                    ['step'=>4,'title'=>'Track trial signups','body'=>'Unique referral URL per creator, UTMs rolled up in analytics.'],
                ],
                'brands'   => ['Notion','Zoho','Freshworks','Unacademy','upGrad','Physics Wallah','ClearTax','Zerodha'],
                'examples' => [
                    ['name'=>'ClearTax',   'result'=>'12 finance creators → 2,400 signups in 30 days','grad'=>'from-cyan-500 to-blue-500'],
                    ['name'=>'upGrad',    'result'=>'Career creator series → 8× ROAS on course revenue','grad'=>'from-indigo-500 to-violet-500'],
                ],
                'testimonial' => ['q'=>'Our SaaS trial signups from creator campaigns beat every other paid channel on cost-per-trial.','name'=>'Head of Growth','company'=>'Indian SaaS company'],
                'faqs' => [
                    ['q'=>'Which platforms work best for SaaS?','a'=>'YouTube for high-consideration, Twitter/X for developer tools, LinkedIn for B2B, TikTok for consumer tools.'],
                    ['q'=>'How do I attribute SaaS trial signups?','a'=>'Unique referral URLs with UTM tracking, generated per creator, rolled up in analytics.'],
                    ['q'=>'Can I run campaigns for hardware?','a'=>'Yes — hardware seeding works great. Ship the device, wait for unboxing + 30-day content. AI review flags missing specs.'],
                    ['q'=>'What about EdTech?','a'=>'Barter (free course access) + revenue share unlocks scale.'],
                    ['q'=>'Do creators need technical expertise?','a'=>'For deep-technical only. Consumer tech (apps, wearables) → general lifestyle creators often win.'],
                ],
            ],

            'travel-and-hospitality' => [
                'title'   => 'Travel & Hospitality',
                'tagline' => 'Destination reels, hotel walkthroughs, itineraries.',
                'emoji'   => '✈️',
                'grad'    => 'from-sky-500 to-indigo-500',
                'accent'  => '#0ea5e9',
                'meta'    => 'Hotels, resorts, tourism boards: run creator campaigns via CreatorFlow with barter stays and POV reels.',
                'hero_kpis' => [['5.6%','Avg engagement'], ['3.8×','Avg ROAS'], ['68%','Barter share']],
                'why_headline' => 'Travel is a barter goldmine',
                'why_body'     => 'A free 2-night stay costs you the empty-room marginal cost. To a mid-tier travel creator it\'s ₹40,000+ perceived value. That asymmetry is why 68% of travel campaigns on CreatorFlow are barter-only.',
                'why_points'   => [
                    ['icon'=>'🛏','title'=>'Off-season weekdays','body'=>'Fill empty rooms with creator stays.'],
                    ['icon'=>'📽','title'=>'POV walkthroughs','body'=>'The single highest-CTR travel format.'],
                    ['icon'=>'💳','title'=>'Direct bookings','body'=>'Discount code at checkout = attributed bookings.'],
                ],
                'playbook' => [
                    ['step'=>1,'title'=>'Pick off-peak inventory','body'=>'Turn dead rooms into content.'],
                    ['step'=>2,'title'=>'Invite travel-only creators','body'=>'Skip generic lifestyle — audience over follower count.'],
                    ['step'=>3,'title'=>'Deliverables','body'=>'1 reel + 5 stories + 1 carousel per stay.'],
                    ['step'=>4,'title'=>'Discount code','body'=>'Creator-branded code for measurable bookings.'],
                ],
                'brands'   => ['SUJÁN','Postcard Hotels','The Lodhi','Zostel','Seven Seas','Roseate','Serenity Hotels','Alila'],
                'examples' => [
                    ['name'=>'Boutique Hotel','result'=>'12 creators × 3 months → 8.4M reach, ₹9.2L direct bookings, zero cash','grad'=>'from-sky-500 to-indigo-500'],
                    ['name'=>'Seven Seas',   'result'=>'Barter + attribution → 3.8× ROAS on all off-season stays','grad'=>'from-cyan-500 to-blue-500'],
                ],
                'testimonial' => ['q'=>'We turned off-season weekdays into a content engine. Zero cash out. Weekend bookings up 34%.','name'=>'GM','company'=>'Boutique hotel, Goa'],
                'faqs' => [
                    ['q'=>'How do I structure a barter stay?','a'=>'Free room + 2 meals for 2 nights, in exchange for 1 reel + 5 stories + 1 IG carousel.'],
                    ['q'=>'International creators?','a'=>'Fully supported. Filter by country in the marketplace; payouts route in local currency.'],
                    ['q'=>'Store-visit campaigns?','a'=>'Yes. Use Store visit — creators check in on Google/Foursquare and drive footfall.'],
                    ['q'=>'How is booking attribution done?','a'=>'Unique discount codes at checkout; or creator-specific landing pages with deep-linking.'],
                    ['q'=>'What formats work?','a'=>'POV walkthroughs, drone shots for resorts, honest room-tour stories. Skip staged fashion content.'],
                ],
            ],

            'home-and-decor' => [
                'title'   => 'Home & Decor',
                'tagline' => 'Room tours, hauls, before/after transformations.',
                'emoji'   => '🏠',
                'grad'    => 'from-amber-500 to-yellow-500',
                'accent'  => '#f59e0b',
                'meta'    => 'Home & decor brands: seed creators for room tours, before/afters, and DIY content.',
                'hero_kpis' => [['6.1%','Avg engagement'], ['4.2×','Avg ROAS'], ['Before/After','Best format']],
                'why_headline' => 'Decor is evergreen — content library compounds',
                'why_body'     => 'Unlike beauty (fast cycle) or fashion (seasonal), home/decor content stays valuable for 6+ months. A great room-tour reel keeps driving discovery long after it posts.',
                'why_points'   => [
                    ['icon'=>'🖼','title'=>'Before / after','body'=>'Highest save rate on IG.'],
                    ['icon'=>'🛋','title'=>'Room tour reels','body'=>'Natural product placement.'],
                    ['icon'=>'🔨','title'=>'DIY tutorials','body'=>'Show the "aha" moment.'],
                ],
                'playbook' => [
                    ['step'=>1,'title'=>'Ship one hero SKU','body'=>'Focus creative on one product per seed.'],
                    ['step'=>2,'title'=>'Home-owner audiences','body'=>'Filter by demo — first-home + parents over-index.'],
                    ['step'=>3,'title'=>'Require before/after','body'=>'Set as mandatory deliverable in the brief.'],
                    ['step'=>4,'title'=>'Reuse for 6 months','body'=>'Every asset stays evergreen.'],
                ],
                'brands'   => ['Wakefit','SleepyCat','Urban Ladder','Pepperfry','Fabindia Home','The White Teak','Sleepwell','Nilkamal'],
                'examples' => [
                    ['name'=>'Wakefit',   'result'=>'20 first-home creators → 340 mattress orders, ₹5.2L revenue','grad'=>'from-amber-500 to-orange-500'],
                    ['name'=>'Urban Ladder','result'=>'Room tour campaign → 12M assisted reach, 6.8% ER','grad'=>'from-yellow-500 to-amber-500'],
                ],
                'testimonial' => ['q'=>'Same 15 creators drove sales for 5 months. That\'s the compound content play.','name'=>'Head of Brand','company'=>'DTC furniture'],
                'faqs' => [
                    ['q'=>'Do you support furniture (large item) seeding?','a'=>'Yes — but acceptance rate is lower and shipping is more logistical. Filter for creators with confirmed home-owner audiences.'],
                    ['q'=>'Can I run "one product, 20 creators" seeds?','a'=>'Yes — single-SKU seeding. Automates order distribution, tracking, and content approval.'],
                    ['q'=>'How do I get before/after content?','a'=>'Include it as required deliverable in your brief. AI review flags missing deliverables.'],
                    ['q'=>'What creator types work?','a'=>'Home + lifestyle + first-home over-index. Skip fashion/beauty creators.'],
                    ['q'=>'Returns / exchanges?','a'=>'Barter seeded items are non-returnable by contract; exchanges (wrong size, defective) go through the messaging thread.'],
                ],
            ],
        ];
    }

    /**
     * Rich data for each campaign-type landing page.
     */
    public static function campaignTypeData(): array
    {
        return [
            'product-review' => [
                'title'   => 'Product Review',
                'tagline' => 'Honest video reviews from verified creators.',
                'emoji'   => '⭐',
                'grad'    => 'from-amber-500 to-orange-500',
                'accent'  => '#f59e0b',
                'meta'    => 'Run authentic product review campaigns with verified creators. AI briefs, contracts, and attribution.',
                'hero_kpis' => [['3–5 min', 'Video length'], ['90 days', 'Usage rights'], ['92%', 'Approval rate']],
                'when_headline' => 'When to run a product review campaign',
                'when_body'     => 'Trust-building creator format. Best for new product launches, category-crowded SKUs, or high-consideration items where buyers spend time in research.',
                'features' => [
                    ['icon'=>'⭐','title'=>'Honest pros / cons','body'=>'Creators shoot in their own voice — not scripted. Higher trust.'],
                    ['icon'=>'🎬','title'=>'3–5 minute deep dive','body'=>'Long enough to cover use, short enough to hold attention.'],
                    ['icon'=>'🛡','title'=>'AI compliance review','body'=>'Auto-flags unverified claims and missing disclosures.'],
                ],
                'process' => [
                    ['step'=>1,'title'=>'Launch','body'=>'Post the campaign, AI drafts the brief.'],
                    ['step'=>2,'title'=>'Ship','body'=>'Auto-Shopify order + tracking.'],
                    ['step'=>3,'title'=>'Wait 7 days','body'=>'Creator uses the product before reviewing.'],
                    ['step'=>4,'title'=>'Approve + measure','body'=>'Watch reviews land, track code redemptions.'],
                ],
                'pricing' => [
                    ['tier'=>'Nano','range'=>'10K–50K','price'=>'₹3,000–₹10,000'],
                    ['tier'=>'Micro','range'=>'50K–250K','price'=>'₹10,000–₹40,000'],
                    ['tier'=>'Mid','range'=>'250K–1M','price'=>'₹40,000–₹1.5L'],
                    ['tier'=>'Macro','range'=>'1M+','price'=>'₹1.5L+'],
                ],
                'faqs' => [
                    ['q'=>'How long does a product review campaign take?','a'=>'Typical: Day 0 launch → Day 3 creator applies → Day 5 shipped → Day 12 review posted. Around 2 weeks.'],
                    ['q'=>'Can I request changes to the review?','a'=>'Yes. Approve, request changes with a comment, or reject. Standard in the contract.'],
                    ['q'=>'What if the creator says something negative?','a'=>'Honest = higher conversion. AI review flags factual errors only.'],
                    ['q'=>'How is the discount code tracked?','a'=>'Unique Shopify code auto-generated at approval time. Every use is attributed.'],
                    ['q'=>'Are usage rights included?','a'=>'90 days paid + organic. Extended usage is negotiable per campaign.'],
                ],
            ],

            'brand-awareness' => [
                'title'   => 'Brand Awareness',
                'tagline' => 'Reach + impressions at scale.',
                'emoji'   => '📣',
                'grad'    => 'from-violet-500 to-fuchsia-500',
                'accent'  => '#a855f7',
                'meta'    => 'Scale brand awareness with a multi-creator burst campaign on CreatorFlow.',
                'hero_kpis' => [['20–50', 'Creators / burst'], ['15–45s', 'Video length'], ['3× ER', 'vs solo book']],
                'when_headline' => 'Awareness campaigns build recall, not conversion',
                'when_body'     => 'Measure success in reach, impressions and assisted conversions — not last-click revenue. Ideal for new brand launches, category expansions, and rebrands.',
                'features' => [
                    ['icon'=>'🔥','title'=>'Multi-creator burst','body'=>'20–50 creators in a single 2-week window creates a cultural moment.'],
                    ['icon'=>'📱','title'=>'Repurpose for paid','body'=>'20-creator burst → 60+ ad-ready assets for Meta/TikTok.'],
                    ['icon'=>'🎯','title'=>'ER-weighted matching','body'=>'CreatorFlow ranks by engagement rate, not follower count.'],
                ],
                'process' => [
                    ['step'=>1,'title'=>'Draft master brief','body'=>'AI generates one brief, personalized per creator.'],
                    ['step'=>2,'title'=>'Book 20–50 creators','body'=>'Single 2-week window for maximum coincidence.'],
                    ['step'=>3,'title'=>'Approve creative','body'=>'Same hook, different creator voices.'],
                    ['step'=>4,'title'=>'Whitelist top 5','body'=>'Convert winning organic assets into paid ads.'],
                ],
                'faqs' => [
                    ['q'=>'How do I measure brand awareness ROI?','a'=>'Reach + impressions + brand-lift study. Reach shows in real-time; brand-lift is a quarterly add-on.'],
                    ['q'=>'How many creators should I book?','a'=>'Launch: 30–50 in the same 2-week window. Maintenance: 10–15 per month.'],
                    ['q'=>'Can I whitelist top performers?','a'=>'Yes. One-click on the assignment page. Creator gets a % share of ad spend.'],
                    ['q'=>'What creator size works best?','a'=>'Multiple micro-creators outperform 1 mega. But 1 mega adds cultural weight — mix both.'],
                    ['q'=>'Cross-platform amplification?','a'=>'Yes — brief once, repurpose across IG Reels, TikTok, YouTube Shorts.'],
                ],
            ],

            'store-visit' => [
                'title'   => 'Store Visit',
                'tagline' => 'Drive real foot traffic.',
                'emoji'   => '📍',
                'grad'    => 'from-cyan-500 to-emerald-500',
                'accent'  => '#06b6d4',
                'meta'    => 'Store visit campaigns: bring local creators into your café, salon, retail store, hotel or restaurant.',
                'hero_kpis' => [['Local', 'Micro-creators'], ['Story+Reel', 'Best combo'], ['QR', 'Attribution']],
                'when_headline' => 'Store visits drive local awareness — fast',
                'when_body'     => 'A local creator with 20K followers in your city outperforms a national creator with 500K when your goal is footfall. Their audience is your buyer.',
                'features' => [
                    ['icon'=>'🍽','title'=>'Restaurants + cafés','body'=>'Food shots, ambience, order recommendations.'],
                    ['icon'=>'💇','title'=>'Salons + spas','body'=>'Experience content, before/after, staff interaction.'],
                    ['icon'=>'🏨','title'=>'Hotels + retail','body'=>'POV walkthroughs, try-on hauls, staff picks.'],
                ],
                'process' => [
                    ['step'=>1,'title'=>'Filter by city','body'=>'Local micro-creators only.'],
                    ['step'=>2,'title'=>'Invite 10','body'=>'Complimentary visit, no cash.'],
                    ['step'=>3,'title'=>'Deliverables','body'=>'5-8 stories + 1 reel + 1 Google review.'],
                    ['step'=>4,'title'=>'QR at billing','body'=>'Redemption code attributes footfall.'],
                ],
                'faqs' => [
                    ['q'=>'How is store-visit attribution measured?','a'=>'QR code at billing, or a unique code redeemed at checkout. Each redemption is attributed to the creator.'],
                    ['q'=>'Do creators travel to my store?','a'=>'Local micro-creators, yes — travel is on them. For invited creators from other cities, factor travel + stay into the barter.'],
                    ['q'=>'What if the creator doesn\'t show up?','a'=>'Contracts include a 48-hour cancellation clause. No-shows lower their performance score.'],
                    ['q'=>'Multiple locations?','a'=>'Yes — separate campaigns per location, or 1 campaign with a "choose your city" flow.'],
                    ['q'=>'Best time slot?','a'=>'Weekday lunch or early dinner (5–7pm) — natural light + no weekend rush.'],
                ],
            ],

            'self-managed' => [
                'title'   => 'Self Managed',
                'tagline' => 'You drive, we power the tools.',
                'emoji'   => '⚡',
                'grad'    => 'from-indigo-500 to-sky-500',
                'accent'  => '#6366f1',
                'meta'    => 'Self-managed influencer marketing platform: unlimited creator search, AI briefs, contracts, attribution.',
                'hero_kpis' => [['100K+', 'Creators'], ['12+', 'Filters'], ['24h', 'To first campaign']],
                'when_headline' => 'For in-house teams that own the strategy',
                'when_body'     => 'You know your brand better than any agency. Self-managed gives you the full CreatorFlow toolkit without the campaign manager.',
                'features' => [
                    ['icon'=>'🔎','title'=>'Unlimited search','body'=>'100K+ verified creators, 12+ filters.'],
                    ['icon'=>'✨','title'=>'AI briefs on demand','body'=>'One prompt → launch-ready brief.'],
                    ['icon'=>'📈','title'=>'Real-time attribution','body'=>'Codes, referrals, multi-touch.'],
                ],
                'process' => [
                    ['step'=>1,'title'=>'Signup','body'=>'Free forever — 5 campaigns/mo on Free plan.'],
                    ['step'=>2,'title'=>'Import products','body'=>'Shopify, CSV, WooCommerce, or manual.'],
                    ['step'=>3,'title'=>'Launch first campaign','body'=>'Average time from signup: 24 hours.'],
                    ['step'=>4,'title'=>'Upgrade when ready','body'=>'Add a campaign manager one-click on Managed.'],
                ],
                'faqs' => [
                    ['q'=>'How many creators can I invite?','a'=>'Unlimited on Growth+. Free plan is 5 campaigns/mo, still unlimited creators per campaign.'],
                    ['q'=>'Do I need a Shopify store?','a'=>'No. CSV, WooCommerce, Amazon, or manual all work. Shopify adds live sync.'],
                    ['q'=>'Can I upgrade to Managed later?','a'=>'Yes — one-click adds a dedicated campaign manager for a retainer.'],
                    ['q'=>'How steep is the learning curve?','a'=>'Most brands ship their first campaign within 24h of signup.'],
                    ['q'=>'Is there a demo?','a'=>'Yes — book a 30-minute demo via contact. Real campaign walkthrough.'],
                ],
            ],

            'barter-campaign' => [
                'title'   => 'Barter Campaign',
                'tagline' => 'Trade product for content.',
                'emoji'   => '🎁',
                'grad'    => 'from-pink-500 to-rose-500',
                'accent'  => '#ec4899',
                'meta'    => 'Barter influencer campaigns: zero cash, 62% accept rate, automatic Shopify orders on CreatorFlow.',
                'hero_kpis' => [['62%', 'Accept rate'], ['4–8×', 'ROAS on retail'], ['Auto', 'Shopify orders']],
                'when_headline' => 'Highest-ROI creator format when done right',
                'when_body'     => 'You pay only the cost of goods. Creators get product they genuinely want. Right playbook returns 4–8× on retail-value spend.',
                'features' => [
                    ['icon'=>'💸','title'=>'Zero cash to creator','body'=>'Product value only. Massive ROI on COGS.'],
                    ['icon'=>'🛒','title'=>'Auto-Shopify order','body'=>'Order + tracking auto-created on approval.'],
                    ['icon'=>'📋','title'=>'Waitlist automation','body'=>'Over-subscribed? Waitlist manages the overflow.'],
                ],
                'process' => [
                    ['step'=>1,'title'=>'Set campaign type = Barter','body'=>'Creator fee stays at 0.'],
                    ['step'=>2,'title'=>'Assume 30% accept rate','body'=>'CreatorFlow invites 3× your target automatically.'],
                    ['step'=>3,'title'=>'Auto-Shopify order','body'=>'Zero manual ops.'],
                    ['step'=>4,'title'=>'Content lands','body'=>'AI review → your approval → live.'],
                ],
                'faqs' => [
                    ['q'=>'Do creators really accept barter?','a'=>'Yes — 62% average across CreatorFlow. Beauty hits 78%, Fashion 55%.'],
                    ['q'=>'What if a creator ghosts?','a'=>'Their performance score drops (visible to all future brands). Ghost rate on CreatorFlow is 4%.'],
                    ['q'=>'Barter + commission hybrid?','a'=>'Yes — set the campaign type to Hybrid. Product + % revenue share.'],
                    ['q'=>'When barter doesn\'t work?','a'=>'Very expensive electronics (₹50K+), services with high delivery cost, products that don\'t photograph well.'],
                    ['q'=>'Max creators per seed?','a'=>'Unlimited. Some brands seed 100–500 creators per launch.'],
                ],
            ],

            'video-shoot' => [
                'title'   => 'Product Videoshoot',
                'tagline' => 'Studio-grade UGC for paid ads.',
                'emoji'   => '🎬',
                'grad'    => 'from-emerald-500 to-teal-500',
                'accent'  => '#10b981',
                'meta'    => 'Studio-quality UGC video from verified creators, ready for paid ads. Multi-format exports, full usage rights.',
                'hero_kpis' => [['3–5', 'Videos / creator'], ['9:16 · 1:1 · 16:9', 'Aspect ratios'], ['14 days', 'Turnaround']],
                'when_headline' => 'When you need ads, not organic content',
                'when_body'     => 'Organic seeding gets you volume. Paid social requires quality: cleaner audio, better cuts, deliberate hooks. That\'s what video-shoot campaigns produce.',
                'features' => [
                    ['icon'=>'📱','title'=>'Multi-format exports','body'=>'9:16 for Reels/TikTok, 1:1 for feed, 16:9 for YouTube pre-roll.'],
                    ['icon'=>'📼','title'=>'Raw + finished','body'=>'You get both the raw footage and the edited cuts.'],
                    ['icon'=>'🔑','title'=>'Full usage rights','body'=>'Perpetual, paid + organic, all platforms.'],
                ],
                'process' => [
                    ['step'=>1,'title'=>'Book pro creators','body'=>'Filter by "Verified" + "Available for paid".'],
                    ['step'=>2,'title'=>'3 hook variations','body'=>'Creators shoot all three for split-testing.'],
                    ['step'=>3,'title'=>'Edit + deliver','body'=>'Around 14 days from creator approval.'],
                    ['step'=>4,'title'=>'Ship to Meta/TikTok Ads','body'=>'Ready-to-upload for every ad platform.'],
                ],
                'faqs' => [
                    ['q'=>'How is this different from barter?','a'=>'Barter = organic-style (creator\'s aesthetic). Videoshoot = ad-ready (your direction, creator\'s face).'],
                    ['q'=>'Do I own the footage?','a'=>'Yes — full usage rights, perpetual, all platforms, paid and organic.'],
                    ['q'=>'Can I approve multiple hook variations?','a'=>'Yes — up to 3 hooks per video. Creators shoot all three, you split-test.'],
                    ['q'=>'What creator type works?','a'=>'Semi-pro creators who\'ve done paid partnerships before.'],
                    ['q'=>'Post-production?','a'=>'Creators deliver edited. If you need additional cuts, add an editor from the partner network.'],
                ],
            ],
        ];
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
                'excerpt' => 'Everything from finding hero products to writing the invite DM.',
                'date' => '2025-05-30', 'read' => '9 min', 'category' => 'Guides',
                'author' => 'Marcus Cole', 'grad' => 'from-cyan-500 to-emerald-500',
            ],
            [
                'slug' => 'ai-briefs-that-dont-sound-ai',
                'title' => 'AI campaign briefs that don\'t sound like AI',
                'excerpt' => 'Five prompt patterns for briefs creators actually love executing.',
                'date' => '2025-05-11', 'read' => '6 min', 'category' => 'Tactics',
                'author' => 'Aria Kim', 'grad' => 'from-amber-500 to-rose-500',
            ],
            [
                'slug' => 'shopify-attribution-fixed',
                'title' => 'Fixing creator attribution on Shopify (finally)',
                'excerpt' => 'How unique codes + referral links + multi-touch tracking tie creators to actual revenue.',
                'date' => '2025-04-22', 'read' => '11 min', 'category' => 'Engineering',
                'author' => 'Devon Patel', 'grad' => 'from-indigo-500 to-violet-500',
            ],
            [
                'slug' => 'creator-rate-cards-benchmarks',
                'title' => 'Creator rate card benchmarks by niche & follower count',
                'excerpt' => 'What UGC, Reels, and full videos cost in 2025.',
                'date' => '2025-04-05', 'read' => '8 min', 'category' => 'Benchmarks',
                'author' => 'Nova Chen', 'grad' => 'from-emerald-500 to-teal-500',
            ],
            [
                'slug' => 'gen-z-creator-brief-template',
                'title' => 'The Gen-Z creator brief template we swear by',
                'excerpt' => 'Short, punchy, non-corporate — and it works.',
                'date' => '2025-03-19', 'read' => '5 min', 'category' => 'Templates',
                'author' => 'Zia Park', 'grad' => 'from-pink-500 to-fuchsia-500',
            ],
        ];
    }
}
