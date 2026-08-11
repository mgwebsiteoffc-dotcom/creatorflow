<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('visits', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('workspace_id')->constrained()->cascadeOnDelete();
            $table->foreignId('creator_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('campaign_id')->nullable()->constrained()->nullOnDelete();
            $table->string('referral_code', 60)->nullable();
            $table->string('utm_source', 120)->nullable();
            $table->string('utm_medium', 120)->nullable();
            $table->string('utm_campaign', 190)->nullable();
            $table->string('landing_url', 500)->nullable();
            $table->char('ip_country', 2)->nullable();
            $table->string('referrer', 500)->nullable();
            $table->timestamp('created_at')->nullable();
            $table->index(['workspace_id', 'created_at']);
            $table->index('creator_id');
        });

        Schema::create('attributions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workspace_id')->constrained()->cascadeOnDelete();
            $table->foreignId('order_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('creator_id')->constrained()->cascadeOnDelete();
            $table->foreignId('campaign_id')->nullable()->constrained()->nullOnDelete();
            $table->unsignedBigInteger('assignment_id')->nullable();
            $table->enum('model', ['first_touch', 'last_touch', 'linear', 'post_purchase_survey', 'discount_code']);
            $table->decimal('weight', 5, 2)->default(1);
            $table->integer('revenue_cents');
            $table->char('currency', 3)->default('USD');
            $table->timestamp('attributed_at');
            $table->timestamp('created_at')->nullable();
            $table->index(['creator_id', 'attributed_at']);
            $table->index(['workspace_id', 'attributed_at']);
        });

        Schema::create('analytics_daily_campaign', function (Blueprint $table) {
            // Composite primary key requires every column NOT NULL.
            // creator_id = 0 represents a campaign-total rollup; a positive
            // value references a creator (no FK here so the aggregate row is
            // valid and creator deletes don't destroy historical rollups).
            $table->foreignId('workspace_id')->constrained()->cascadeOnDelete();
            $table->foreignId('campaign_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('creator_id')->default(0);
            $table->date('date');
            $table->integer('impressions')->default(0);
            $table->integer('clicks')->default(0);
            $table->integer('content_count')->default(0);
            $table->integer('orders')->default(0);
            $table->integer('revenue_cents')->default(0);
            $table->integer('commission_cents')->default(0);
            $table->integer('product_cost_cents')->default(0);
            $table->integer('fee_cents')->default(0);
            $table->primary(['workspace_id', 'campaign_id', 'creator_id', 'date'], 'adc_pk');
            $table->index('creator_id');
        });

        Schema::create('ai_runs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workspace_id')->nullable()->constrained()->nullOnDelete();
            $table->string('subject_type', 100);
            $table->unsignedBigInteger('subject_id');
            $table->string('task', 100);
            $table->string('model', 60);
            $table->longText('prompt')->nullable();
            $table->longText('response')->nullable();
            $table->integer('tokens_in')->nullable();
            $table->integer('tokens_out')->nullable();
            $table->integer('cost_cents')->default(0);
            $table->enum('status', ['queued', 'running', 'completed', 'failed'])->default('queued');
            $table->integer('latency_ms')->nullable();
            $table->timestamps();
            $table->index(['subject_type', 'subject_id']);
            $table->index('workspace_id');
        });

        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workspace_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('actor_type', ['user', 'creator', 'system', 'ai'])->nullable();
            $table->unsignedBigInteger('actor_id')->nullable();
            $table->string('action', 120);
            $table->string('subject_type', 100)->nullable();
            $table->unsignedBigInteger('subject_id')->nullable();
            $table->json('changes')->nullable();
            $table->string('ip', 45)->nullable();
            $table->timestamp('created_at')->nullable();
            $table->index(['workspace_id', 'action', 'created_at']);
        });

        Schema::create('event_log', function (Blueprint $table) {
            $table->id();
            $table->string('event', 120);
            $table->string('aggregate_type', 100)->nullable();
            $table->unsignedBigInteger('aggregate_id')->nullable();
            $table->json('payload')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->index(['event', 'created_at']);
            $table->index(['aggregate_type', 'aggregate_id']);
        });

        Schema::create('outbound_webhooks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workspace_id')->constrained()->cascadeOnDelete();
            $table->string('url', 500);
            $table->string('secret');
            $table->json('events')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('outbound_webhooks');
        Schema::dropIfExists('event_log');
        Schema::dropIfExists('audit_logs');
        Schema::dropIfExists('ai_runs');
        Schema::dropIfExists('analytics_daily_campaign');
        Schema::dropIfExists('attributions');
        Schema::dropIfExists('visits');
    }
};
