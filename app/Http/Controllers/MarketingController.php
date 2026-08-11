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
            ],
            'beauty-and-cosmetics' => [
                'title' => 'Beauty &amp; Cosmetics',
                'tagline' => 'Skin-first content, honest reviews, quick trials',
                'emoji' => '💄', 'grad' => 'from-rose-500 to-orange-500',
                'why'   => 'Beauty is the #1 seeding category on CreatorFlow. Acceptance rates hit 62% on average and CVR beats every other niche.',
                'stats' => [['Avg ER', '8.1%'], ['Avg ROAS', '7.2×'], ['Best format', 'GRWM Reels']],
            ],
            'food-and-fitness' => [
                'title' => 'Food &amp; Fitness',
                'tagline' => 'Recipes, workouts, sampling that converts',
                'emoji' => '🥗', 'grad' => 'from-emerald-500 to-teal-500',
                'why'   => 'Foodies over-index on repeat purchase. Fitness creators unlock high-intent cohorts that scale into subscriptions.',
                'stats' => [['Avg ER', '9.4%'], ['Avg ROAS', '4.6×'], ['Best format', 'Tutorial']],
            ],
            'tech-and-education' => [
                'title' => 'Tech &amp; Education',
                'tagline' => 'Long-form trust, demo-first video',
                'emoji' => '💻', 'grad' => 'from-cyan-500 to-blue-500',
                'why'   => 'YouTube integrations for tech buyers hit 9× ROAS. EdTech converts best via 30-45s hook + carousel.',
                'stats' => [['Avg ER', '4.2%'], ['Avg ROAS', '9.1×'], ['Best format', 'YT integration']],
            ],
            'travel-and-hospitality' => [
                'title' => 'Travel &amp; Hospitality',
                'tagline' => 'Destination reels, hotel walkthroughs, itineraries',
                'emoji' => '✈️', 'grad' => 'from-sky-500 to-indigo-500',
                'why'   => 'Barter-only campaigns (property stays) drive massive volume — 40 creators for zero cash outlay is standard.',
                'stats' => [['Avg ER', '5.6%'], ['Avg ROAS', '3.8×'], ['Best format', 'POV Reel']],
            ],
            'home-and-decor' => [
                'title' => 'Home &amp; Decor',
                'tagline' => 'Room tours, hauls, before/after transformations',
                'emoji' => '🏠', 'grad' => 'from-amber-500 to-yellow-500',
                'why'   => 'DIY & decor tags drive the highest saves rate on IG. Great for evergreen catalog seeding.',
                'stats' => [['Avg ER', '6.1%'], ['Avg ROAS', '4.2×'], ['Best format', 'Before/After']],
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
            ],
            'brand-awareness' => [
                'title' => 'Brand Awareness',
                'tagline' => 'Reach + impressions at scale',
                'emoji' => '📣', 'grad' => 'from-violet-500 to-fuchsia-500',
                'why'   => 'Story-led campaigns designed to leave a mark. Reach the right audience at scale in their feed.',
                'bullets' => ['15–45s hero video', 'Multi-creator burst', 'ER-weighted matching'],
            ],
            'store-visit' => [
                'title' => 'Store Visit',
                'tagline' => 'Drive real foot traffic',
                'emoji' => '📍', 'grad' => 'from-cyan-500 to-emerald-500',
                'why'   => 'Invite local creators IRL — capture the vibe, drive footfall and win local word of mouth.',
                'bullets' => ['Local micro-creators only', 'Story + Reel combo', 'Attribution via QR / redemption'],
            ],
            'self-managed' => [
                'title' => 'Self Managed',
                'tagline' => 'You drive, we power the tools',
                'emoji' => '⚡', 'grad' => 'from-indigo-500 to-sky-500',
                'why'   => 'Full access to the platform: creator DB, briefs, contracts, attribution. You run the show.',
                'bullets' => ['Unlimited creator search', 'AI briefs on demand', 'Real-time attribution'],
            ],
            'barter-campaign' => [
                'title' => 'Barter Campaign',
                'tagline' => 'Trade product for content',
                'emoji' => '🎁', 'grad' => 'from-pink-500 to-rose-500',
                'why'   => 'A cost-effective way to generate authentic UGC at scale. 62% average acceptance rate.',
                'bullets' => ['Zero cash to creator', 'Auto Shopify order + tracking', 'Waitlist automation'],
            ],
            'video-shoot' => [
                'title' => 'Product Videoshoot',
                'tagline' => 'Studio-grade UGC for paid ads',
                'emoji' => '🎬', 'grad' => 'from-emerald-500 to-teal-500',
                'why'   => 'High-quality creator-produced video ready for paid social, DTC pages and marketplaces.',
                'bullets' => ['Multi-angle deliverables', 'Ad-format exports (9:16, 1:1, 16:9)', 'Full usage rights'],
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
