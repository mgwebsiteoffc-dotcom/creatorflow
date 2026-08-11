<?php

namespace App\View\Composers;

use App\Models\HomepageItem;
use App\Support\SchemaCheck;
use Illuminate\View\View;

class HomepageComposer
{
    /**
     * Default client logos and reels used when no HomepageItem rows exist
     * or before the migration is run. Client names lifted from the
     * SuperVyral list + our existing brand demo names.
     */
    public static function defaultClientLogos(): array
    {
        // These are text-only "logos" (bg gradient + brand name). Admin can
        // upload real transparent PNGs to override them via /admin/homepage.
        return [
            'MX Player','DailyHunt','Advanta','Corteva','Absolute','Times Internet',
            'Dow','DuPont','Syngenta','NDTV','HT Digital','Nykaa Fashion',
            'Glow & Co.','Roving Mode','Fable Street','Bewakoof','Evereve','Nicobar',
        ];
    }

    public static function defaultReels(): array
    {
        return [
            ['Samsara Ghee',      'Food',        'from-amber-400 to-orange-500',  '2.4M views',  '@nova.eats'],
            ['Luxotica Perfume',  'Cosmetics',   'from-fuchsia-400 to-pink-500',  '840K views',  '@aria.k'],
            ['Seven Seas Travel', 'Travel',      'from-cyan-400 to-blue-500',     '1.1M views',  '@theovlog'],
            ['Perfume+',          'Cosmetics',   'from-rose-400 to-red-500',      '620K views',  '@mira.reels'],
            ['Atul Bakery',       'Store Visit', 'from-yellow-400 to-amber-500',  '410K views',  '@foodie.desi'],
            ['Evereve Lifestyle', 'Lifestyle',   'from-violet-500 to-purple-600', '1.8M views',  '@zia.styles'],
            ['Roving Mode',       'Fashion',     'from-indigo-500 to-violet-500', '960K views',  '@fashioncore'],
            ['Iva Lens',          'Eyewear',     'from-emerald-400 to-teal-500',  '580K views',  '@techkai'],
        ];
    }

    public function compose(View $view): void
    {
        // Pull from DB if available; otherwise fall back to defaults.
        if (SchemaCheck::has('homepage_items')) {
            $clientLogos = HomepageItem::active()->section('client_logo')->orderBy('position')->get();
            $reels       = HomepageItem::active()->section('sample_reel')->orderBy('position')->get();
        } else {
            $clientLogos = collect();
            $reels       = collect();
        }

        // Normalize reels to a common structure for the view
        $reelsData = $reels->isNotEmpty()
            ? $reels->map(fn ($r) => [
                'title'      => $r->title,
                'category'   => $r->category ?: 'Featured',
                'gradient'   => $r->gradient ?: 'from-violet-500 to-pink-500',
                'meta'       => $r->meta ?: 'Featured',
                'creator'    => $r->subtitle ?: '',
                'video_url'  => $r->video_path,
                'poster_url' => $r->poster_path,
                'link'       => $r->external_url,
            ])->all()
            : collect(static::defaultReels())->map(fn ($r) => [
                'title'      => $r[0],
                'category'   => $r[1],
                'gradient'   => $r[2],
                'meta'       => $r[3],
                'creator'    => $r[4],
                'video_url'  => null,
                'poster_url' => null,
                'link'       => null,
            ])->all();

        $logoData = $clientLogos->isNotEmpty()
            ? $clientLogos->map(fn ($l) => [
                'name'    => $l->title,
                'url'     => $l->image_path,
                'link'    => $l->external_url,
            ])->all()
            : collect(static::defaultClientLogos())->map(fn ($name) => [
                'name'    => $name,
                'url'     => null,
                'link'    => null,
            ])->all();

        $categories = collect($reelsData)->pluck('category')->unique()->values()->all();

        $view->with([
            'reelsData'  => $reelsData,
            'logoData'   => $logoData,
            'categories' => $categories,
        ]);
    }
}
