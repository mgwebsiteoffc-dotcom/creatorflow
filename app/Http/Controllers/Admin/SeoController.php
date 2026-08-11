<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use App\Support\SchemaCheck;
use App\Support\SeoData;

class SeoController extends Controller
{
    public function __invoke()
    {
        $services = SeoData::services();
        $cities   = SeoData::cities();
        $blogPosts = SchemaCheck::has('blog_posts') ? BlogPost::published()->get() : collect();

        // Build the full indexable URL list — same rules as the sitemap.
        $rows = collect();

        $rows->push(['url' => url('/'),                      'group' => 'Core',       'title' => 'Homepage']);
        $rows->push(['url' => route('features'),             'group' => 'Core',       'title' => 'Features']);
        $rows->push(['url' => route('pricing'),              'group' => 'Core',       'title' => 'Pricing']);
        $rows->push(['url' => route('about'),                'group' => 'Core',       'title' => 'About']);
        $rows->push(['url' => route('contact'),              'group' => 'Core',       'title' => 'Contact']);
        $rows->push(['url' => route('tools.index'),          'group' => 'Tools',      'title' => 'Free tools']);
        $rows->push(['url' => route('tools.roi'),            'group' => 'Tools',      'title' => 'ROI calculator']);
        $rows->push(['url' => route('tools.rate'),           'group' => 'Tools',      'title' => 'Creator rate calculator']);
        $rows->push(['url' => route('tools.brief'),          'group' => 'Tools',      'title' => 'Brief generator']);
        $rows->push(['url' => route('resources'),            'group' => 'Core',       'title' => 'Resources']);
        $rows->push(['url' => route('blog.index'),           'group' => 'Blog',       'title' => 'Blog index']);
        $rows->push(['url' => route('services.index'),       'group' => 'Services',   'title' => 'Services index']);

        foreach (['fashion-and-lifestyle','beauty-and-cosmetics','food-and-fitness','tech-and-education','travel-and-hospitality','home-and-decor'] as $slug) {
            $rows->push(['url' => route('industry.show', $slug), 'group' => 'Industry', 'title' => 'Industry: '.$slug]);
        }
        foreach (['product-review','brand-awareness','store-visit','self-managed','barter-campaign','video-shoot'] as $slug) {
            $rows->push(['url' => route('campaign-type.show', $slug), 'group' => 'Campaign type', 'title' => 'Campaign: '.$slug]);
        }
        foreach ($services as $sSlug => $svc) {
            $rows->push(['url' => route('services.show', $sSlug), 'group' => 'Service', 'title' => $svc['name']]);
            foreach ($cities as $cSlug => $c) {
                $rows->push([
                    'url'   => route('services.city', [$sSlug, $cSlug]),
                    'group' => 'Service × City',
                    'title' => $svc['name'].' in '.$c['name'],
                ]);
            }
        }
        foreach ($blogPosts as $post) {
            $rows->push(['url' => route('blog.show', $post->slug), 'group' => 'Blog post', 'title' => $post->title]);
        }

        $counts = $rows->groupBy('group')->map->count();

        // Keyword coverage — which target keywords we address
        $targetKeywords = [
            'influencer marketing agency in delhi'      => route('services.city', ['influencer-marketing-agency', 'delhi']),
            'ugc influencers in delhi'                  => route('services.city', ['ugc-influencers', 'delhi']),
            'barter influencers in delhi'               => route('services.city', ['barter-influencers', 'delhi']),
            'influencer marketing agency in mumbai'     => route('services.city', ['influencer-marketing-agency', 'mumbai']),
            'ugc influencers in mumbai'                 => route('services.city', ['ugc-influencers', 'mumbai']),
            'barter influencers in mumbai'              => route('services.city', ['barter-influencers', 'mumbai']),
            'micro influencer marketing in bangalore'   => route('services.city', ['micro-influencer-marketing', 'bangalore']),
            'shopify influencer marketing in india'     => route('services.city', ['shopify-influencer-marketing', 'india']),
            'creator seeding for DTC brands'            => route('services.show', 'creator-seeding'),
            'creator marketplace india'                 => route('services.show', 'creator-marketplace'),
        ];

        return view('admin.seo.index', compact('rows', 'counts', 'targetKeywords', 'services', 'cities'));
    }
}
