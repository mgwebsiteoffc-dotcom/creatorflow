<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('creators', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('user_id')->nullable()->unique()->constrained()->nullOnDelete();
            $table->string('display_name');
            $table->string('slug')->unique();
            $table->string('avatar_path')->nullable();
            $table->text('bio')->nullable();
            $table->string('email')->nullable();
            $table->string('phone', 40)->nullable();
            $table->char('country', 2)->nullable();
            $table->string('city', 120)->nullable();
            $table->json('languages')->nullable();
            $table->json('niches')->nullable();
            $table->string('status', 40)->default('pending');
            $table->boolean('open_to_work')->default(true);
            $table->boolean('accepts_barter')->default(true);
            $table->boolean('accepts_paid')->default(true);
            $table->boolean('accepts_affiliate')->default(true);
            $table->integer('rate_ugc_cents')->nullable();
            $table->integer('rate_post_cents')->nullable();
            $table->integer('rate_video_cents')->nullable();
            $table->integer('rate_story_cents')->nullable();
            $table->char('currency', 3)->default('USD');
            $table->integer('follower_count_total')->default(0);
            $table->decimal('engagement_rate', 5, 2)->default(0);
            $table->integer('avg_views')->default(0);
            $table->decimal('performance_score', 5, 2)->default(0);
            $table->decimal('fraud_risk', 5, 2)->default(0);
            $table->string('stripe_connect_id')->nullable();
            $table->string('payout_method_status', 40)->default('unverified');
            $table->text('ai_summary')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['status', 'open_to_work']);
        });

        Schema::create('creator_niches', function (Blueprint $table) {
            $table->foreignId('creator_id')->constrained()->cascadeOnDelete();
            $table->string('niche', 120);
            $table->primary(['creator_id', 'niche']);
            $table->index('niche');
        });

        Schema::create('creator_social_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('creator_id')->constrained()->cascadeOnDelete();
            $table->enum('platform', ['instagram', 'tiktok', 'youtube', 'x', 'facebook', 'linkedin', 'pinterest', 'blog', 'other']);
            $table->string('handle', 190);
            $table->string('url')->nullable();
            $table->integer('follower_count')->default(0);
            $table->decimal('engagement_rate', 5, 2)->default(0);
            $table->integer('avg_views')->default(0);
            $table->boolean('verified')->default(false);
            $table->json('metadata')->nullable();
            $table->timestamp('last_synced_at')->nullable();
            $table->timestamps();
            $table->unique(['platform', 'handle']);
            $table->index('creator_id');
        });

        Schema::create('creator_portfolio_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('creator_id')->constrained()->cascadeOnDelete();
            $table->enum('type', ['image', 'video', 'link', 'embed', 'case_study']);
            $table->string('title')->nullable();
            $table->string('disk', 40)->default('local');
            $table->string('path', 500)->nullable();
            $table->string('external_url', 500)->nullable();
            $table->string('thumbnail_path', 500)->nullable();
            $table->text('description')->nullable();
            $table->json('metrics')->nullable();
            $table->integer('position')->default(0);
            $table->timestamps();
            $table->index(['creator_id', 'position']);
        });

        Schema::create('creator_preferences', function (Blueprint $table) {
            $table->foreignId('creator_id')->primary()->constrained()->cascadeOnDelete();
            $table->json('barter_product_categories')->nullable();
            $table->integer('min_paid_cents')->default(0);
            $table->json('shipping_address')->nullable();
            $table->string('clothing_size', 40)->nullable();
            $table->string('skin_tone', 40)->nullable();
            $table->string('hair_type', 40)->nullable();
            $table->enum('availability_status', ['available', 'busy', 'inactive'])->default('available');
            $table->integer('response_time_hours')->nullable();
            $table->json('notifications_json')->nullable();
            $table->timestamps();

        });

        Schema::create('creator_audience_snapshots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('creator_id')->constrained()->cascadeOnDelete();
            $table->foreignId('social_account_id')->nullable()->constrained('creator_social_accounts')->nullOnDelete();
            $table->date('snapshot_date');
            $table->integer('followers')->nullable();
            $table->decimal('engagement', 5, 2)->nullable();
            $table->integer('avg_views')->nullable();
            $table->json('age_buckets')->nullable();
            $table->json('gender_split')->nullable();
            $table->json('top_countries')->nullable();
            $table->json('top_cities')->nullable();
            $table->decimal('fake_follower_pct', 5, 2)->default(0);
            $table->timestamp('created_at')->nullable();
            $table->index(['creator_id', 'snapshot_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('creator_audience_snapshots');
        Schema::dropIfExists('creator_preferences');
        Schema::dropIfExists('creator_portfolio_items');
        Schema::dropIfExists('creator_social_accounts');
        Schema::dropIfExists('creator_niches');
        Schema::dropIfExists('creators');
    }
};
