<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('push_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->cascadeOnDelete();
            $table->foreignId('creator_id')->nullable()->constrained()->cascadeOnDelete();
            $table->text('endpoint');
            $table->text('p256dh')->nullable();
            $table->text('auth_token')->nullable();
            $table->string('user_agent', 255)->nullable();
            $table->timestamp('last_seen_at')->nullable();
            $table->timestamps();
            $table->index('user_id');
            $table->index('creator_id');
        });

        // Also make room for VAPID keys in platform_settings.
        if (Schema::hasTable('platform_settings')) {
            Schema::table('platform_settings', function (Blueprint $table) {
                if (! Schema::hasColumn('platform_settings', 'vapid_public_key'))  $table->text('vapid_public_key')->nullable();
                if (! Schema::hasColumn('platform_settings', 'vapid_private_key')) $table->text('vapid_private_key')->nullable();
                if (! Schema::hasColumn('platform_settings', 'vapid_subject'))     $table->string('vapid_subject')->nullable(); // mailto:you@example.com
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('push_subscriptions');
        if (Schema::hasTable('platform_settings')) {
            Schema::table('platform_settings', function (Blueprint $table) {
                foreach (['vapid_public_key','vapid_private_key','vapid_subject'] as $c) {
                    if (Schema::hasColumn('platform_settings', $c)) $table->dropColumn($c);
                }
            });
        }
    }
};
