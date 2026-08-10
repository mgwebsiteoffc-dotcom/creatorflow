<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contracts', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('workspace_id')->constrained()->cascadeOnDelete();
            $table->foreignId('campaign_id')->constrained()->cascadeOnDelete();
            $table->foreignId('creator_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->longText('body');
            $table->json('usage_rights')->nullable();
            $table->integer('fee_cents')->default(0);
            $table->enum('status', ['draft', 'sent', 'viewed', 'signed', 'declined', 'expired'])->default('draft');
            $table->timestamp('signed_by_creator_at')->nullable();
            $table->timestamp('signed_by_brand_at')->nullable();
            $table->string('signature_ip', 45)->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
            $table->index(['workspace_id', 'status']);
        });

        Schema::create('discount_codes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workspace_id')->constrained()->cascadeOnDelete();
            $table->foreignId('campaign_id')->constrained()->cascadeOnDelete();
            $table->foreignId('creator_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
            $table->string('code', 60);
            $table->enum('type', ['percentage', 'fixed_amount', 'free_shipping', 'full_comp']);
            $table->decimal('value', 8, 2);
            $table->string('external_id')->nullable();
            $table->integer('usage_limit')->default(1);
            $table->integer('times_used')->default(0);
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->enum('status', ['active', 'disabled', 'expired'])->default('active');
            $table->timestamps();
            $table->unique(['workspace_id', 'code']);
            $table->index('campaign_id');
        });

        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('workspace_id')->constrained()->cascadeOnDelete();
            $table->foreignId('channel_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('creator_id')->constrained()->cascadeOnDelete();
            $table->string('external_id')->nullable();
            $table->string('order_number', 60)->nullable();
            $table->string('email', 190)->nullable();
            $table->integer('subtotal_cents')->default(0);
            $table->integer('total_discount_cents')->default(0);
            $table->integer('total_cents')->default(0);
            $table->char('currency', 3)->default('USD');
            $table->enum('status', ['draft', 'open', 'paid', 'fulfilled', 'cancelled', 'refunded', 'returned'])->default('draft');
            $table->json('shipping_address')->nullable();
            $table->string('tracking_number', 190)->nullable();
            $table->string('tracking_company', 190)->nullable();
            $table->timestamp('placed_at')->nullable();
            $table->timestamp('fulfilled_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->json('raw_payload')->nullable();
            $table->timestamps();
            $table->index(['workspace_id', 'status']);
            $table->index(['channel_id', 'external_id']);
        });

        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('variant_id')->nullable()->constrained('product_variants')->nullOnDelete();
            $table->string('external_line_id')->nullable();
            $table->string('title');
            $table->string('sku', 190)->nullable();
            $table->integer('quantity')->default(1);
            $table->integer('price_cents')->default(0);
            $table->integer('total_discount_cents')->default(0);
            $table->timestamps();
            $table->index('order_id');
        });

        Schema::create('campaign_assignments', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('campaign_id')->constrained()->cascadeOnDelete();
            $table->foreignId('campaign_product_id')->constrained('campaign_products')->cascadeOnDelete();
            $table->foreignId('creator_id')->constrained()->cascadeOnDelete();
            $table->enum('status', [
                'accepted', 'contract_sent', 'contract_signed',
                'order_created', 'shipped', 'delivered',
                'in_progress', 'submitted', 'changes_requested',
                'approved', 'completed', 'cancelled',
            ])->default('accepted');
            $table->string('discount_code', 60)->nullable();
            $table->foreignId('channel_order_id')->nullable()->constrained('orders')->nullOnDelete();
            $table->foreignId('contract_id')->nullable()->constrained()->nullOnDelete();
            $table->date('content_due_date')->nullable();
            $table->integer('fee_cents')->default(0);
            $table->decimal('commission_rate', 5, 2)->nullable();
            $table->unsignedBigInteger('payout_id')->nullable();
            $table->json('tracking')->nullable();
            $table->timestamps();
            $table->unique(['campaign_id', 'creator_id', 'campaign_product_id'], 'assign_unique');
            $table->index(['creator_id', 'status']);
            $table->index(['status', 'content_due_date']);
        });

        Schema::create('content_submissions', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('assignment_id')->constrained('campaign_assignments')->cascadeOnDelete();
            $table->foreignId('creator_id')->constrained()->cascadeOnDelete();
            $table->foreignId('campaign_id')->constrained()->cascadeOnDelete();
            $table->enum('type', ['image', 'video', 'story', 'reel', 'link', 'caption_draft']);
            $table->string('disk', 40)->default('local');
            $table->string('path', 500);
            $table->string('thumbnail_path', 500)->nullable();
            $table->text('caption')->nullable();
            $table->string('external_post_url', 500)->nullable();
            $table->json('metadata')->nullable();
            $table->decimal('ai_score', 5, 2)->nullable();
            $table->json('ai_feedback')->nullable();
            $table->json('ai_flags')->nullable();
            $table->enum('status', ['submitted', 'in_review', 'changes_requested', 'approved', 'rejected', 'published'])->default('submitted');
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
            $table->index('assignment_id');
            $table->index(['creator_id', 'status']);
        });

        Schema::create('content_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('content_submission_id')->constrained()->cascadeOnDelete();
            $table->enum('reviewer_type', ['brand', 'ai']);
            $table->foreignId('reviewer_id')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('decision', ['approved', 'changes_requested', 'rejected']);
            $table->text('comment')->nullable();
            $table->json('ai_annotations')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->index('content_submission_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('content_reviews');
        Schema::dropIfExists('content_submissions');
        Schema::dropIfExists('campaign_assignments');
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('discount_codes');
        Schema::dropIfExists('contracts');
    }
};
