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
            // Mail (transactional email)
            if (! Schema::hasColumn('platform_settings', 'mail_driver'))         $table->string('mail_driver', 20)->default('log')->after('ai_last_test_status'); // log | resend | mailersend | smtp
            if (! Schema::hasColumn('platform_settings', 'mail_api_key'))        $table->text('mail_api_key')->nullable()->after('mail_driver');
            if (! Schema::hasColumn('platform_settings', 'mail_from_address'))   $table->string('mail_from_address')->nullable()->after('mail_api_key');
            if (! Schema::hasColumn('platform_settings', 'mail_from_name'))      $table->string('mail_from_name')->nullable()->after('mail_from_address');
            if (! Schema::hasColumn('platform_settings', 'mail_reply_to'))       $table->string('mail_reply_to')->nullable()->after('mail_from_name');
            if (! Schema::hasColumn('platform_settings', 'mail_last_tested_at')) $table->timestamp('mail_last_tested_at')->nullable()->after('mail_reply_to');
            if (! Schema::hasColumn('platform_settings', 'mail_last_test_status')) $table->string('mail_last_test_status', 40)->nullable()->after('mail_last_tested_at');

            // Analytics + verification (site-wide scripts)
            if (! Schema::hasColumn('platform_settings', 'ga4_measurement_id'))    $table->string('ga4_measurement_id', 40)->nullable()->after('mail_last_test_status'); // G-XXXXXXX
            if (! Schema::hasColumn('platform_settings', 'gtm_container_id'))      $table->string('gtm_container_id', 40)->nullable()->after('ga4_measurement_id');       // GTM-XXXXXX
            if (! Schema::hasColumn('platform_settings', 'meta_pixel_id'))         $table->string('meta_pixel_id', 40)->nullable()->after('gtm_container_id');
            if (! Schema::hasColumn('platform_settings', 'linkedin_partner_id'))   $table->string('linkedin_partner_id', 40)->nullable()->after('meta_pixel_id');
            if (! Schema::hasColumn('platform_settings', 'hotjar_id'))             $table->string('hotjar_id', 40)->nullable()->after('linkedin_partner_id');
            if (! Schema::hasColumn('platform_settings', 'google_site_verification')) $table->string('google_site_verification', 100)->nullable()->after('hotjar_id');
            if (! Schema::hasColumn('platform_settings', 'bing_site_verification'))   $table->string('bing_site_verification', 100)->nullable()->after('google_site_verification');
            if (! Schema::hasColumn('platform_settings', 'custom_head_html'))      $table->text('custom_head_html')->nullable()->after('bing_site_verification');   // paste-any-tag escape hatch
            if (! Schema::hasColumn('platform_settings', 'custom_body_html'))      $table->text('custom_body_html')->nullable()->after('custom_head_html');

            // Payments (Razorpay)
            if (! Schema::hasColumn('platform_settings', 'razorpay_key_id'))       $table->string('razorpay_key_id', 80)->nullable()->after('custom_body_html');
            if (! Schema::hasColumn('platform_settings', 'razorpay_key_secret'))   $table->text('razorpay_key_secret')->nullable()->after('razorpay_key_id');
            if (! Schema::hasColumn('platform_settings', 'razorpay_webhook_secret')) $table->text('razorpay_webhook_secret')->nullable()->after('razorpay_key_secret');
            if (! Schema::hasColumn('platform_settings', 'razorpay_mode'))         $table->string('razorpay_mode', 10)->default('test')->after('razorpay_webhook_secret'); // test | live
            if (! Schema::hasColumn('platform_settings', 'razorpay_last_tested_at')) $table->timestamp('razorpay_last_tested_at')->nullable()->after('razorpay_mode');
            if (! Schema::hasColumn('platform_settings', 'razorpay_last_test_status')) $table->string('razorpay_last_test_status', 40)->nullable()->after('razorpay_last_tested_at');

            // Feature toggles for the roadmap items (admin can flip on/off)
            if (! Schema::hasColumn('platform_settings', 'features_json'))          $table->json('features_json')->nullable()->after('razorpay_last_test_status');
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('platform_settings')) return;
        Schema::table('platform_settings', function (Blueprint $table) {
            $cols = [
                'mail_driver','mail_api_key','mail_from_address','mail_from_name','mail_reply_to','mail_last_tested_at','mail_last_test_status',
                'ga4_measurement_id','gtm_container_id','meta_pixel_id','linkedin_partner_id','hotjar_id',
                'google_site_verification','bing_site_verification','custom_head_html','custom_body_html',
                'razorpay_key_id','razorpay_key_secret','razorpay_webhook_secret','razorpay_mode','razorpay_last_tested_at','razorpay_last_test_status',
                'features_json',
            ];
            foreach ($cols as $c) if (Schema::hasColumn('platform_settings', $c)) $table->dropColumn($c);
        });
    }
};
