<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

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

    public function blogIndex()  { return view('marketing.blog.index', ['posts' => $this->posts()]); }

    public function blogShow(string $slug)
    {
        $posts = $this->posts();
        $post = collect($posts)->firstWhere('slug', $slug);
        abort_unless($post, 404);

        return view('marketing.blog.show', [
            'post' => $post,
            'related' => collect($posts)->where('slug', '!=', $slug)->take(3)->values(),
        ]);
    }

    protected function posts(): array
    {
        return [
            [
                'slug' => 'seeding-vs-paid-ugc-2025',
                'title' => 'Seeding vs. paid UGC in 2025: which one actually converts?',
                'excerpt' => 'A data-backed breakdown of when barter beats paid, when it doesn\'t, and how to blend the two for maximum ROAS.',
                'date' => '2025-06-18',
                'read' => '7 min',
                'category' => 'Playbooks',
                'author' => 'Priya Sharma',
                'grad' => 'from-violet-500 to-pink-500',
            ],
            [
                'slug' => 'first-100-creators-checklist',
                'title' => 'The first 100 creators: a launch checklist for DTC founders',
                'excerpt' => 'Everything from finding hero products to writing the invite DM, with templates you can copy today.',
                'date' => '2025-05-30',
                'read' => '9 min',
                'category' => 'Guides',
                'author' => 'Marcus Cole',
                'grad' => 'from-cyan-500 to-emerald-500',
            ],
            [
                'slug' => 'ai-briefs-that-dont-sound-ai',
                'title' => 'AI campaign briefs that don\'t sound like AI',
                'excerpt' => 'Five prompt patterns we use inside CreatorFlow to generate briefs creators actually love executing.',
                'date' => '2025-05-11',
                'read' => '6 min',
                'category' => 'Tactics',
                'author' => 'Aria Kim',
                'grad' => 'from-amber-500 to-rose-500',
            ],
            [
                'slug' => 'shopify-attribution-fixed',
                'title' => 'Fixing creator attribution on Shopify (finally)',
                'excerpt' => 'How unique codes + referral links + multi-touch tracking come together to tie creators to actual revenue.',
                'date' => '2025-04-22',
                'read' => '11 min',
                'category' => 'Engineering',
                'author' => 'Devon Patel',
                'grad' => 'from-indigo-500 to-violet-500',
            ],
            [
                'slug' => 'creator-rate-cards-benchmarks',
                'title' => 'Creator rate card benchmarks by niche &amp; follower count',
                'excerpt' => 'What UGC, Reels, and full videos cost in 2025 — a benchmark from 12,000+ CreatorFlow deals.',
                'date' => '2025-04-05',
                'read' => '8 min',
                'category' => 'Benchmarks',
                'author' => 'Nova Chen',
                'grad' => 'from-emerald-500 to-teal-500',
            ],
            [
                'slug' => 'gen-z-creator-brief-template',
                'title' => 'The Gen‑Z creator brief template we swear by',
                'excerpt' => 'Short, punchy, non‑corporate — and it works. Copy the exact template we use for viral drops.',
                'date' => '2025-03-19',
                'read' => '5 min',
                'category' => 'Templates',
                'author' => 'Zia Park',
                'grad' => 'from-pink-500 to-fuchsia-500',
            ],
        ];
    }
}
