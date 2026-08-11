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
