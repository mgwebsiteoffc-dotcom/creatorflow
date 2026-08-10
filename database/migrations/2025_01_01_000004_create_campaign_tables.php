<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('campaigns', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('workspace_id')->constrained()->cascadeOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('title');
            $table->enum('type', ['barter', 'paid', 'affiliate', 'hybrid']);
            $table->enum('status', ['draft', 'matching', 'inviting', 'active', 'paused', 'completed', 'cancelled'])->default('draft');
            $table->string('niche', 120)->nullable();
            $table->text('summary')->nullable();
            $table->longText('brief')->nullable();
            $table->json('objectives')->nullable();
            $table->json('content_types')->nullable();
            $table->json('deliverables')->nullable();
            $table->json('usage_rights')->nullable();
            $table->json('exclusivity')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->integer('budget_total_cents')->default(0);
            $table->char('budget_currency', 3)->default('USD');
            $table->integer('creator_fee_cents')->default(0);
            $table->integer('product_cost_cents')->default(0);
            $table->decimal('commission_rate', 5, 2)->default(0);
            $table->integer('target_creators')->default(0);
            $table->integer('invite_pool_size')->default(0);
            $table->decimal('acceptance_rate_assumed', 5, 2)->default(30);
            $table->integer('waitlist_size')->default(0);
            $table->boolean('ai_generated')->default(false);
            $table->decimal('ai_predicted_roi', 8, 2)->nullable();
            $table->json('ai_metadata')->nullable();
            $table->timestamp('launched_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index(['workspace_id', 'status']);
            $table->index(['type', 'status']);
        });

        Schema::create('campaign_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('variant_id')->nullable()->constrained('product_variants')->nullOnDelete();
            $table->integer('target_creators')->default(1);
            $table->integer('accepted_count')->default(0);
            $table->integer('shipped_count')->default(0);
            $table->integer('content_received_count')->default(0);
            $table->integer('fee_cents')->default(0);
            $table->decimal('commission_rate', 5, 2)->nullable();
            $table->json('settings')->nullable();
            $table->timestamps();
            $table->unique(['campaign_id', 'product_id', 'variant_id'], 'cp_unique');
            $table->index('campaign_id');
        });

        Schema::create('campaign_creator_matches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')->constrained()->cascadeOnDelete();
            $table->foreignId('creator_id')->constrained()->cascadeOnDelete();
            $table->decimal('score', 5, 2);
            $table->json('reasons')->nullable();
            $table->json('predicted_performance')->nullable();
            $table->enum('status', ['candidate', 'invited', 'accepted', 'declined', 'waitlisted', 'rejected'])->default('candidate');
            $table->timestamp('invited_at')->nullable();
            $table->timestamp('responded_at')->nullable();
            $table->timestamps();
            $table->unique(['campaign_id', 'creator_id']);
            $table->index(['campaign_id', 'status']);
            $table->index(['campaign_id', 'score']);
        });

        Schema::create('campaign_invitations', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('campaign_id')->constrained()->cascadeOnDelete();
            $table->foreignId('creator_id')->constrained()->cascadeOnDelete();
            $table->foreignId('campaign_product_id')->nullable()->constrained('campaign_products')->nullOnDelete();
            $table->enum('channel', ['email', 'in_app', 'sms', 'push'])->default('in_app');
            $table->text('message')->nullable();
            $table->string('ai_variant', 40)->nullable();
            $table->enum('status', ['queued', 'sent', 'opened', 'accepted', 'declined', 'expired'])->default('queued');
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('opened_at')->nullable();
            $table->timestamp('responded_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
            $table->index(['campaign_id', 'status']);
            $table->index('creator_id');
        });

        Schema::create('applications', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('campaign_id')->constrained()->cascadeOnDelete();
            $table->foreignId('creator_id')->constrained()->cascadeOnDelete();
            $table->text('cover_note')->nullable();
            $table->integer('proposed_fee_cents')->nullable();
            $table->enum('status', ['submitted', 'shortlisted', 'approved', 'rejected', 'withdrawn'])->default('submitted');
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();
            $table->unique(['campaign_id', 'creator_id']);
            $table->index(['campaign_id', 'status']);
        });

        Schema::create('waitlist_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')->constrained()->cascadeOnDelete();
            $table->foreignId('campaign_product_id')->nullable()->constrained('campaign_products')->nullOnDelete();
            $table->foreignId('creator_id')->constrained()->cascadeOnDelete();
            $table->integer('position');
            $table->enum('status', ['waiting', 'promoted', 'expired', 'removed'])->default('waiting');
            $table->timestamp('promoted_at')->nullable();
            $table->timestamps();
            $table->unique(['campaign_id', 'creator_id']);
            $table->index(['campaign_id', 'status', 'position']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('waitlist_entries');
        Schema::dropIfExists('applications');
        Schema::dropIfExists('campaign_invitations');
        Schema::dropIfExists('campaign_creator_matches');
        Schema::dropIfExists('campaign_products');
        Schema::dropIfExists('campaigns');
    }
};
