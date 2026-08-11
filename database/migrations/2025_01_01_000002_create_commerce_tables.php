<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('channels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workspace_id')->constrained()->cascadeOnDelete();
            $table->enum('type', ['shopify', 'woocommerce', 'amazon', 'csv', 'manual', 'api']);
            $table->string('name')->nullable();
            $table->string('external_id')->nullable();
            $table->text('credentials')->nullable();
            $table->json('settings')->nullable();
            $table->string('status', 40)->default('active');
            $table->timestamp('last_synced_at')->nullable();
            $table->timestamp('last_full_sync_at')->nullable();
            $table->integer('sync_errors')->default(0);
            $table->timestamps();
            $table->softDeletes();
            $table->index(['workspace_id', 'type']);
            $table->index(['type', 'external_id']);
        });

        Schema::create('sync_jobs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('channel_id')->constrained()->cascadeOnDelete();
            $table->foreignId('workspace_id')->constrained()->cascadeOnDelete();
            $table->string('type', 60);
            $table->enum('mode', ['incremental', 'full'])->default('incremental');
            $table->enum('status', ['queued', 'running', 'completed', 'failed'])->default('queued');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('finished_at')->nullable();
            $table->integer('processed')->default(0);
            $table->integer('errors')->default(0);
            $table->longText('error_log')->nullable();
            $table->timestamps();
            $table->index(['workspace_id', 'status']);
        });

        Schema::create('channel_webhooks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('channel_id')->constrained()->cascadeOnDelete();
            $table->string('external_id')->nullable();
            $table->string('topic', 120);
            $table->string('address');
            $table->string('status', 40)->default('active');
            $table->timestamps();
            $table->index('channel_id');
        });

        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('workspace_id')->constrained()->cascadeOnDelete();
            $table->foreignId('channel_id')->nullable()->constrained()->nullOnDelete();
            $table->string('external_id')->nullable();
            $table->string('title');
            $table->longText('description')->nullable();
            $table->string('vendor')->nullable();
            $table->string('product_type')->nullable();
            $table->string('niche', 120)->nullable();
            $table->decimal('hero_score', 5, 2)->default(0);
            $table->string('status', 40)->default('active');
            $table->json('tags')->nullable();
            $table->json('metadata')->nullable();
            $table->json('ai_analysis')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['workspace_id', 'status']);
            $table->index(['channel_id', 'external_id']);
        });

        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('external_id')->nullable();
            $table->string('sku')->nullable();
            $table->string('title')->nullable();
            $table->integer('price_cents')->default(0);
            $table->integer('compare_at_cents')->nullable();
            $table->char('currency', 3)->default('USD');
            $table->integer('inventory_qty')->default(0);
            $table->string('inventory_policy', 40)->default('continue');
            $table->string('barcode', 60)->nullable();
            $table->decimal('weight', 10, 2)->nullable();
            $table->boolean('requires_shipping')->default(true);
            $table->timestamps();
            $table->index('product_id');
            $table->index('sku');
        });

        Schema::create('product_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('variant_id')->nullable()->constrained('product_variants')->nullOnDelete();
            $table->string('disk', 40)->default('local');
            $table->string('path', 500);
            $table->string('alt')->nullable();
            $table->integer('position')->default(0);
            $table->boolean('is_primary')->default(false);
            $table->integer('width')->nullable();
            $table->integer('height')->nullable();
            $table->timestamps();
            $table->index(['product_id', 'position']);
        });

        Schema::create('collections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workspace_id')->constrained()->cascadeOnDelete();
            $table->string('external_id')->nullable();
            $table->string('title');
            $table->string('slug');
            $table->text('description')->nullable();
            $table->timestamps();
            $table->unique(['workspace_id', 'slug']);
        });

        Schema::create('collection_product', function (Blueprint $table) {
            $table->foreignId('collection_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->integer('position')->default(0);
            $table->primary(['collection_id', 'product_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('collection_product');
        Schema::dropIfExists('collections');
        Schema::dropIfExists('product_images');
        Schema::dropIfExists('product_variants');
        Schema::dropIfExists('products');
        Schema::dropIfExists('channel_webhooks');
        Schema::dropIfExists('sync_jobs');
        Schema::dropIfExists('channels');
    }
};
