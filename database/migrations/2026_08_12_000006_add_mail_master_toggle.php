<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('platform_settings')) return;
        Schema::table('platform_settings', function (Blueprint $table) {
            if (! Schema::hasColumn('platform_settings', 'mail_enabled')) {
                // Master kill-switch for all outbound email. When false, nothing sends
                // regardless of per-template email_enabled toggles.
                $table->boolean('mail_enabled')->default(true)->after('metadata');
            }
            if (! Schema::hasColumn('platform_settings', 'inapp_enabled')) {
                $table->boolean('inapp_enabled')->default(true)->after('mail_enabled');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('platform_settings')) return;
        Schema::table('platform_settings', function (Blueprint $table) {
            foreach (['mail_enabled', 'inapp_enabled'] as $c) {
                if (Schema::hasColumn('platform_settings', $c)) $table->dropColumn($c);
            }
        });
    }
};
