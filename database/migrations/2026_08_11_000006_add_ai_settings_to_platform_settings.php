<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('platform_settings')) {
            return; // Wait for the earlier migration to create the table.
        }
        Schema::table('platform_settings', function (Blueprint $table) {
            if (! Schema::hasColumn('platform_settings', 'ai_driver')) {
                $table->string('ai_driver', 20)->default('fake')->after('metadata'); // fake | openai
            }
            if (! Schema::hasColumn('platform_settings', 'ai_openai_key')) {
                $table->text('ai_openai_key')->nullable()->after('ai_driver');
            }
            if (! Schema::hasColumn('platform_settings', 'ai_openai_model')) {
                $table->string('ai_openai_model', 80)->default('gpt-4o-mini')->after('ai_openai_key');
            }
            if (! Schema::hasColumn('platform_settings', 'ai_openai_embedding_model')) {
                $table->string('ai_openai_embedding_model', 80)->default('text-embedding-3-small')->after('ai_openai_model');
            }
            if (! Schema::hasColumn('platform_settings', 'ai_base_url')) {
                $table->string('ai_base_url')->nullable()->after('ai_openai_embedding_model'); // for Azure / proxies
            }
            if (! Schema::hasColumn('platform_settings', 'ai_temperature')) {
                $table->decimal('ai_temperature', 3, 2)->default(0.40)->after('ai_base_url');
            }
            if (! Schema::hasColumn('platform_settings', 'ai_last_tested_at')) {
                $table->timestamp('ai_last_tested_at')->nullable()->after('ai_temperature');
            }
            if (! Schema::hasColumn('platform_settings', 'ai_last_test_status')) {
                $table->string('ai_last_test_status', 40)->nullable()->after('ai_last_tested_at');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('platform_settings')) return;
        Schema::table('platform_settings', function (Blueprint $table) {
            foreach ([
                'ai_driver', 'ai_openai_key', 'ai_openai_model', 'ai_openai_embedding_model',
                'ai_base_url', 'ai_temperature', 'ai_last_tested_at', 'ai_last_test_status',
            ] as $c) {
                if (Schema::hasColumn('platform_settings', $c)) $table->dropColumn($c);
            }
        });
    }
};
