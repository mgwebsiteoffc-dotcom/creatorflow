<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * Convenience seeder that only creates demo data (same as DatabaseSeeder).
 * Registered so `php artisan db:seed --class=DemoSeeder` works without
 * touching other seeders.
 */
class DemoSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(DatabaseSeeder::class);
    }
}
