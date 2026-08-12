<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── Whatify (WhatsApp BSP) creds on platform_settings ──
        if (Schema::hasTable('platform_settings')) {
            Schema::table('platform_settings', function (Blueprint $table) {
                if (! Schema::hasColumn('platform_settings', 'whatify_enabled'))       $table->boolean('whatify_enabled')->default(false);
                if (! Schema::hasColumn('platform_settings', 'whatify_api_key'))       $table->text('whatify_api_key')->nullable();
                if (! Schema::hasColumn('platform_settings', 'whatify_base_url'))      $table->string('whatify_base_url')->default('https://app.whatify.in');
                if (! Schema::hasColumn('platform_settings', 'whatify_account_id'))    $table->string('whatify_account_id', 80)->nullable();
                if (! Schema::hasColumn('platform_settings', 'whatify_from_number'))   $table->string('whatify_from_number', 30)->nullable();
                if (! Schema::hasColumn('platform_settings', 'whatify_last_tested_at')) $table->timestamp('whatify_last_tested_at')->nullable();
                if (! Schema::hasColumn('platform_settings', 'whatify_last_test_status')) $table->string('whatify_last_test_status', 40)->nullable();
            });
        }

        // ── Notification templates (email subject/html + wa template mapping) ──
        Schema::create('notification_templates', function (Blueprint $table) {
            $table->id();
            $table->string('event_key', 80)->unique();          // e.g. 'creator.invited', 'order.shipped'
            $table->string('audience', 20)->default('creator'); // creator | brand | admin
            $table->string('label');                            // human name
            $table->string('description')->nullable();

            // Email
            $table->boolean('email_enabled')->default(true);
            $table->string('email_subject')->nullable();
            $table->longText('email_body')->nullable();         // {{ placeholders }}

            // WhatsApp (Whatify)
            $table->boolean('whatsapp_enabled')->default(false);
            $table->string('whatsapp_template_name', 120)->nullable();
            $table->json('whatsapp_body_params')->nullable();   // e.g. ["{{creator_name}}","{{campaign_title}}"]

            // In-app
            $table->boolean('inapp_enabled')->default(true);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notification_templates');
        if (Schema::hasTable('platform_settings')) {
            Schema::table('platform_settings', function (Blueprint $table) {
                foreach ([
                    'whatify_enabled','whatify_api_key','whatify_base_url','whatify_account_id',
                    'whatify_from_number','whatify_last_tested_at','whatify_last_test_status',
                ] as $c) if (Schema::hasColumn('platform_settings', $c)) $table->dropColumn($c);
            });
        }
    }
};
