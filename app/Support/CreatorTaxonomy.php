<?php

namespace App\Support;

/**
 * Shared taxonomy for creator targeting: tiers, cities, genders, ages, languages.
 * Keep this the single source of truth so the marketplace filter and the
 * campaign audience picker stay in sync.
 */
class CreatorTaxonomy
{
    /** Tier is derived from follower count. */
    public static function tiers(): array
    {
        return [
            'nano'  => ['label' => 'Nano',  'range' => '1K – 10K',   'min' => 1_000,    'max' => 10_000,    'note' => 'Hyper-loyal community, highest ER'],
            'micro' => ['label' => 'Micro', 'range' => '10K – 100K', 'min' => 10_000,   'max' => 100_000,   'note' => 'Best-in-class conversion'],
            'mid'   => ['label' => 'Mid',   'range' => '100K – 500K','min' => 100_000,  'max' => 500_000,   'note' => 'Reach + credibility balance'],
            'macro' => ['label' => 'Macro', 'range' => '500K – 1M',  'min' => 500_000,  'max' => 1_000_000, 'note' => 'Broad awareness'],
            'mega'  => ['label' => 'Mega',  'range' => '1M+',        'min' => 1_000_000,'max' => null,      'note' => 'Celebrity-scale reach'],
        ];
    }

    public static function tierFromFollowers(?int $followers): ?string
    {
        $followers = (int) $followers;
        if ($followers < 1_000)     return null;
        if ($followers < 10_000)    return 'nano';
        if ($followers < 100_000)   return 'micro';
        if ($followers < 500_000)   return 'mid';
        if ($followers < 1_000_000) return 'macro';
        return 'mega';
    }

    /** Indian metros + Tier-2 hubs + International. Slugs match SeoData::cities() where possible. */
    public static function cities(): array
    {
        return [
            'delhi'      => ['name' => 'Delhi',      'region' => 'Delhi NCR'],
            'mumbai'     => ['name' => 'Mumbai',     'region' => 'Maharashtra'],
            'bangalore'  => ['name' => 'Bangalore',  'region' => 'Karnataka'],
            'hyderabad'  => ['name' => 'Hyderabad',  'region' => 'Telangana'],
            'chennai'    => ['name' => 'Chennai',    'region' => 'Tamil Nadu'],
            'pune'       => ['name' => 'Pune',       'region' => 'Maharashtra'],
            'kolkata'    => ['name' => 'Kolkata',    'region' => 'West Bengal'],
            'ahmedabad'  => ['name' => 'Ahmedabad',  'region' => 'Gujarat'],
            'jaipur'     => ['name' => 'Jaipur',     'region' => 'Rajasthan'],
            'gurugram'   => ['name' => 'Gurugram',   'region' => 'Delhi NCR'],
            'noida'      => ['name' => 'Noida',      'region' => 'Delhi NCR'],
            'lucknow'    => ['name' => 'Lucknow',    'region' => 'Uttar Pradesh'],
            'chandigarh' => ['name' => 'Chandigarh', 'region' => 'Chandigarh'],
            'indore'     => ['name' => 'Indore',     'region' => 'Madhya Pradesh'],
            'kochi'      => ['name' => 'Kochi',      'region' => 'Kerala'],
            'goa'        => ['name' => 'Goa',        'region' => 'Goa'],
            'surat'      => ['name' => 'Surat',      'region' => 'Gujarat'],
            'bhopal'     => ['name' => 'Bhopal',     'region' => 'Madhya Pradesh'],
        ];
    }

    /** Convenience: `[slug => 'City, Region']` for `<option>` labels. */
    public static function cityOptions(): array
    {
        return collect(static::cities())
            ->map(fn ($c) => $c['name'].', '.$c['region'])
            ->all();
    }

    public static function genders(): array
    {
        return [
            'female'     => 'Female',
            'male'       => 'Male',
            'non_binary' => 'Non-binary',
            'other'      => 'Other',
        ];
    }

    public static function ageRanges(): array
    {
        return [
            '13-17' => '13 – 17',
            '18-24' => '18 – 24',
            '25-34' => '25 – 34',
            '35-44' => '35 – 44',
            '45-54' => '45 – 54',
            '55+'   => '55+',
        ];
    }

    public static function languages(): array
    {
        return [
            'en' => 'English',
            'hi' => 'Hindi',
            'ta' => 'Tamil',
            'te' => 'Telugu',
            'kn' => 'Kannada',
            'ml' => 'Malayalam',
            'mr' => 'Marathi',
            'gu' => 'Gujarati',
            'bn' => 'Bengali',
            'pa' => 'Punjabi',
            'ur' => 'Urdu',
        ];
    }
}
