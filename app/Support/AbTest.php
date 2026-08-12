<?php

namespace App\Support;

use App\Models\AbExperiment;
use App\Models\PlatformSetting;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AbTest
{
    /**
     * Pick the variant for the given experiment slug, log an impression,
     * and return the variant array. Returns null when disabled.
     */
    public static function variant(string $slug): ?array
    {
        if (! PlatformSetting::feature('ab_testing')) return null;
        if (! SchemaCheck::has('ab_experiments'))    return null;

        $exp = AbExperiment::where('slug', $slug)->where('status', 'running')->first();
        if (! $exp) return null;

        $visitorId = Cookie::get('ab_visitor');
        if (! $visitorId) {
            $visitorId = (string) Str::uuid();
            // Long-lived cookie so bucket assignment is sticky
            Cookie::queue('ab_visitor', $visitorId, 60 * 24 * 90);
        }

        $variant = $exp->pickFor($visitorId);

        try {
            DB::table('ab_events')->insert([
                'experiment_id' => $exp->id,
                'variant_key'   => $variant['key'] ?? 'A',
                'event'         => 'impression',
                'visitor_id'    => $visitorId,
                'created_at'    => now(),
            ]);
        } catch (\Throwable) { /* ignore */ }

        return $variant + ['experiment_slug' => $slug];
    }

    /**
     * Log a conversion for the given experiment (using the current visitor bucket).
     */
    public static function convert(string $slug): void
    {
        if (! PlatformSetting::feature('ab_testing')) return;
        if (! SchemaCheck::has('ab_experiments'))    return;
        $exp = AbExperiment::where('slug', $slug)->first();
        if (! $exp) return;
        $visitorId = Cookie::get('ab_visitor');
        if (! $visitorId) return;
        $variant = $exp->pickFor($visitorId);
        try {
            DB::table('ab_events')->insert([
                'experiment_id' => $exp->id,
                'variant_key'   => $variant['key'] ?? 'A',
                'event'         => 'conversion',
                'visitor_id'    => $visitorId,
                'created_at'    => now(),
            ]);
        } catch (\Throwable) {}
    }
}
