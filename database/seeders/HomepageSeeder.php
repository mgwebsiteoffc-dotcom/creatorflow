<?php

namespace Database\Seeders;

use App\Models\HomepageItem;
use App\Support\SchemaCheck;
use Illuminate\Database\Seeder;

class HomepageSeeder extends Seeder
{
    public function run(): void
    {
        if (! SchemaCheck::has('homepage_items')) {
            $this->command?->warn('homepage_items table not migrated — skipping.');
            return;
        }

        // If the admin has already customized, don't stomp their entries.
        if (HomepageItem::exists()) {
            $this->command?->info('homepage_items already populated — skipping.');
            return;
        }

        $logos = [
            'MX Player','DailyHunt','Advanta','Corteva','Absolute','Times Internet',
            'Dow','DuPont','Syngenta','NDTV','HT Digital','Nykaa Fashion',
            'Glow & Co.','Roving Mode','Fable Street','Bewakoof','Evereve','Nicobar',
        ];
        foreach ($logos as $i => $name) {
            HomepageItem::create([
                'section'   => 'client_logo',
                'title'     => $name,
                'position'  => $i,
                'is_active' => true,
            ]);
        }

        $reels = [
            ['Samsara Ghee',      'Food',        'from-amber-400 to-orange-500',  '2.4M views',  '@nova.eats'],
            ['Luxotica Perfume',  'Cosmetics',   'from-fuchsia-400 to-pink-500',  '840K views',  '@aria.k'],
            ['Seven Seas Travel', 'Travel',      'from-cyan-400 to-blue-500',     '1.1M views',  '@theovlog'],
            ['Perfume+',          'Cosmetics',   'from-rose-400 to-red-500',      '620K views',  '@mira.reels'],
            ['Atul Bakery',       'Store Visit', 'from-yellow-400 to-amber-500',  '410K views',  '@foodie.desi'],
            ['Evereve Lifestyle', 'Lifestyle',   'from-violet-500 to-purple-600', '1.8M views',  '@zia.styles'],
            ['Roving Mode',       'Fashion',     'from-indigo-500 to-violet-500', '960K views',  '@fashioncore'],
            ['Iva Lens',          'Eyewear',     'from-emerald-400 to-teal-500',  '580K views',  '@techkai'],
        ];
        foreach ($reels as $i => $r) {
            HomepageItem::create([
                'section'   => 'sample_reel',
                'title'     => $r[0],
                'category'  => $r[1],
                'gradient'  => $r[2],
                'meta'      => $r[3],
                'subtitle'  => $r[4],
                'position'  => $i,
                'is_active' => true,
            ]);
        }

        $this->command?->info('Seeded '.count($logos).' client logos + '.count($reels).' sample reels.');
    }
}
