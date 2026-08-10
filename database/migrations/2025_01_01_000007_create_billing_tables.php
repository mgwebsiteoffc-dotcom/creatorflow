<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workspace_id')->constrained()->cascadeOnDelete();
            $table->enum('provider', ['stripe', 'shopify']);
            $table->string('provider_id')->nullable();
            $table->string('plan', 40);
            $table->string('status', 40);
            $table->integer('quantity')->default(1);
            $table->timestamp('trial_ends_at')->nullable();
            $table->timestamp('current_period_start')->nullable();
            $table->timestamp('current_period_end')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamps();
            $table->index(['workspace_id', 'status']);
        });

        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workspace_id')->constrained()->cascadeOnDelete();
            $table->foreignId('subscription_id')->nullable()->constrained()->nullOnDelete();
            $table->string('provider', 40);
            $table->string('provider_invoice_id')->nullable();
            $table->integer('amount_cents');
            $table->char('currency', 3)->default('USD');
            $table->string('status', 40);
            $table->timestamp('due_at')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->string('pdf_path', 500)->nullable();
            $table->timestamps();
            $table->index(['workspace_id', 'status']);
        });

        Schema::create('payouts', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('creator_id')->constrained()->cascadeOnDelete();
            $table->foreignId('workspace_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('assignment_id')->nullable();
            $table->integer('amount_cents');
            $table->char('currency', 3)->default('USD');
            $table->integer('platform_fee_cents')->default(0);
            $table->integer('processing_fee_cents')->default(0);
            $table->integer('net_cents');
            $table->enum('method', ['stripe_connect', 'bank', 'paypal', 'manual'])->default('stripe_connect');
            $table->enum('status', ['pending', 'in_transit', 'paid', 'failed', 'cancelled'])->default('pending');
            $table->string('external_transfer_id')->nullable();
            $table->timestamp('scheduled_for')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->index(['creator_id', 'status']);
            $table->index(['workspace_id', 'status']);
        });

        // Backfill the forward reference from campaign_assignments -> payouts.
        Schema::table('campaign_assignments', function (Blueprint $table) {
            $table->foreign('payout_id')->references('id')->on('payouts')->nullOnDelete();
        });

        Schema::create('marketplace_commissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workspace_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('assignment_id')->nullable();
            $table->foreignId('order_id')->nullable()->constrained()->nullOnDelete();
            $table->integer('amount_cents');
            $table->char('currency', 3)->default('USD');
            $table->decimal('rate', 5, 2);
            $table->enum('status', ['pending', 'held', 'released', 'waived', 'refunded'])->default('pending');
            $table->timestamps();
            $table->index(['workspace_id', 'status']);
        });

        Schema::create('usage_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workspace_id')->constrained()->cascadeOnDelete();
            $table->string('metric', 60);
            $table->string('period', 7); // YYYY-MM
            $table->integer('used')->default(0);
            $table->timestamps();
            $table->unique(['workspace_id', 'metric', 'period']);
        });
    }

    public function down(): void
    {
        Schema::table('campaign_assignments', function (Blueprint $table) {
            $table->dropForeign(['payout_id']);
        });
        Schema::dropIfExists('usage_records');
        Schema::dropIfExists('marketplace_commissions');
        Schema::dropIfExists('payouts');
        Schema::dropIfExists('invoices');
        Schema::dropIfExists('subscriptions');
    }
};
