<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── RazorpayX (payouts) creds on platform_settings ──
        if (Schema::hasTable('platform_settings')) {
            Schema::table('platform_settings', function (Blueprint $table) {
                if (! Schema::hasColumn('platform_settings', 'razorpayx_enabled'))         $table->boolean('razorpayx_enabled')->default(false);
                if (! Schema::hasColumn('platform_settings', 'razorpayx_account_number')) $table->string('razorpayx_account_number', 40)->nullable(); // virtual account number
                if (! Schema::hasColumn('platform_settings', 'razorpayx_mode'))            $table->string('razorpayx_mode', 12)->default('IMPS');    // IMPS | NEFT | UPI | RTGS
                if (! Schema::hasColumn('platform_settings', 'razorpayx_last_tested_at'))  $table->timestamp('razorpayx_last_tested_at')->nullable();
                if (! Schema::hasColumn('platform_settings', 'razorpayx_last_test_status')) $table->string('razorpayx_last_test_status', 40)->nullable();
            });
        }

        // ── Creator bank / UPI details for payouts ──
        if (Schema::hasTable('creators')) {
            Schema::table('creators', function (Blueprint $table) {
                if (! Schema::hasColumn('creators', 'payout_method'))            $table->string('payout_method', 12)->default('upi')->after('payout_method_status'); // upi | bank
                if (! Schema::hasColumn('creators', 'upi_vpa'))                  $table->string('upi_vpa', 120)->nullable();
                if (! Schema::hasColumn('creators', 'bank_account_holder_name')) $table->string('bank_account_holder_name', 190)->nullable();
                if (! Schema::hasColumn('creators', 'bank_account_number'))      $table->string('bank_account_number', 40)->nullable();
                if (! Schema::hasColumn('creators', 'bank_ifsc'))                $table->string('bank_ifsc', 20)->nullable();
                if (! Schema::hasColumn('creators', 'pan_number'))               $table->string('pan_number', 20)->nullable();
                if (! Schema::hasColumn('creators', 'razorpayx_contact_id'))     $table->string('razorpayx_contact_id', 60)->nullable();
                if (! Schema::hasColumn('creators', 'razorpayx_fund_account_id')) $table->string('razorpayx_fund_account_id', 60)->nullable();
            });
        }

        // ── Payout row bookkeeping (external id + provider) ──
        if (Schema::hasTable('payouts')) {
            Schema::table('payouts', function (Blueprint $table) {
                if (! Schema::hasColumn('payouts', 'provider'))            $table->string('provider', 20)->default('manual')->after('method'); // razorpayx | stripe_connect | manual
                if (! Schema::hasColumn('payouts', 'external_id'))         $table->string('external_id', 80)->nullable();
                if (! Schema::hasColumn('payouts', 'external_status'))     $table->string('external_status', 40)->nullable();
                if (! Schema::hasColumn('payouts', 'failure_reason'))      $table->string('failure_reason', 200)->nullable();
                if (! Schema::hasColumn('payouts', 'processed_at'))        $table->timestamp('processed_at')->nullable();
            });
        }

        // ── Instagram Graph API creds per creator ──
        if (Schema::hasTable('creator_social_accounts')) {
            Schema::table('creator_social_accounts', function (Blueprint $table) {
                if (! Schema::hasColumn('creator_social_accounts', 'graph_access_token')) $table->text('graph_access_token')->nullable();
                if (! Schema::hasColumn('creator_social_accounts', 'graph_business_id'))  $table->string('graph_business_id', 60)->nullable();
                if (! Schema::hasColumn('creator_social_accounts', 'graph_page_id'))      $table->string('graph_page_id', 60)->nullable();
                if (! Schema::hasColumn('creator_social_accounts', 'graph_ig_user_id'))   $table->string('graph_ig_user_id', 60)->nullable();
                if (! Schema::hasColumn('creator_social_accounts', 'graph_synced_at'))    $table->timestamp('graph_synced_at')->nullable();
            });
        }

        // ── Meta / Instagram admin creds on platform_settings ──
        if (Schema::hasTable('platform_settings')) {
            Schema::table('platform_settings', function (Blueprint $table) {
                if (! Schema::hasColumn('platform_settings', 'instagram_app_id'))     $table->string('instagram_app_id', 40)->nullable();
                if (! Schema::hasColumn('platform_settings', 'instagram_app_secret')) $table->text('instagram_app_secret')->nullable();
                if (! Schema::hasColumn('platform_settings', 'instagram_enabled'))    $table->boolean('instagram_enabled')->default(false);
                if (! Schema::hasColumn('platform_settings', 'sentry_dsn'))           $table->text('sentry_dsn')->nullable();
                if (! Schema::hasColumn('platform_settings', 'sentry_environment'))   $table->string('sentry_environment', 20)->default('production');
                if (! Schema::hasColumn('platform_settings', 'sentry_traces_sample')) $table->decimal('sentry_traces_sample', 3, 2)->default(0.20);
            });
        }
    }

    public function down(): void
    {
        // Deliberately narrow — dropping cols is destructive; comment out to invoke.
    }
};
