<?php

namespace App\Support;

use Illuminate\Support\Facades\Schema;

/**
 * Per-request cached wrapper around Schema::hasTable(). Prevents dozens of
 * SHOW TABLES round-trips when a controller checks the same table from
 * several places, and gives us one clear seam to fake tables in tests.
 */
class SchemaCheck
{
    protected static array $cache = [];

    public static function has(string $table): bool
    {
        if (! array_key_exists($table, static::$cache)) {
            try {
                static::$cache[$table] = Schema::hasTable($table);
            } catch (\Throwable) {
                static::$cache[$table] = false;
            }
        }

        return static::$cache[$table];
    }

    /** Clear the in-request cache (useful in tests). */
    public static function reset(): void
    {
        static::$cache = [];
    }
}
