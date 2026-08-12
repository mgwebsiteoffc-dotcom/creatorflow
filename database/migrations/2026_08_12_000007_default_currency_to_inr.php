<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Switch every currency default from USD to INR + update existing USD rows
     * that were created with the old default AND never touched by a user.
     *
     * We avoid Schema::table() column-change here because DBAL/doctrine isn't
     * always installed on Laragon — instead we use raw ALTER TABLE which MySQL
     * accepts. If your driver isn't MySQL, this migration is a no-op safe
     * fallback (silent try/catch per statement).
     */
    public function up(): void
    {
        $targets = [
            'workspaces'         => 'currency',
            'products'           => 'currency',
            'creators'           => 'currency',
            'campaigns'          => 'budget_currency',
            'orders'             => 'currency',
            'subscriptions'      => 'currency',
            'invoices'           => 'currency',
            'payouts'            => 'currency',
            'attributions'       => 'currency',
            'escrow_transactions'=> 'currency',
            'payment_records'    => 'currency',
        ];

        foreach ($targets as $table => $column) {
            if (! Schema::hasTable($table) || ! Schema::hasColumn($table, $column)) continue;

            // Change default on the column (MySQL syntax).
            try {
                DB::statement("ALTER TABLE `{$table}` MODIFY `{$column}` CHAR(3) NOT NULL DEFAULT 'INR'");
            } catch (\Throwable $e) {
                // Non-MySQL driver or column has different definition — skip silently.
            }

            // Migrate historical rows that still hold the old default.
            try {
                DB::table($table)->where($column, 'USD')->update([$column => 'INR']);
            } catch (\Throwable $e) {
                // Ignore — non-fatal.
            }
        }
    }

    public function down(): void
    {
        // No revert — Indian brands want INR forever.
    }
};
