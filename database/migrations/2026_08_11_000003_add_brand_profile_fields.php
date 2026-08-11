<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('workspaces', function (Blueprint $table) {
            $table->string('legal_name')->nullable()->after('name');
            $table->string('contact_email')->nullable()->after('website');
            $table->string('contact_phone', 40)->nullable()->after('contact_email');
            $table->string('address_line1')->nullable()->after('country');
            $table->string('address_line2')->nullable()->after('address_line1');
            $table->string('address_city', 120)->nullable()->after('address_line2');
            $table->string('address_state', 120)->nullable()->after('address_city');
            $table->string('address_postal', 30)->nullable()->after('address_state');
            $table->string('tax_type', 20)->nullable()->after('address_postal');  // gstin | vat | ein | none
            $table->string('tax_id', 60)->nullable()->after('tax_type');
            $table->text('billing_notes')->nullable()->after('tax_id');
        });

        // Payment records logged by the brand (manual entries too — real Stripe
        // records still land in invoices via webhooks).
        Schema::create('payment_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workspace_id')->constrained()->cascadeOnDelete();
            $table->foreignId('campaign_id')->nullable()->constrained()->nullOnDelete();
            $table->string('reference')->nullable();
            $table->string('description')->nullable();
            $table->enum('kind', ['subscription', 'campaign', 'top_up', 'refund', 'adjustment'])->default('campaign');
            $table->enum('direction', ['inflow', 'outflow'])->default('outflow'); // outflow = brand paid us
            $table->integer('amount_cents');
            $table->char('currency', 3)->default('USD');
            $table->enum('status', ['pending', 'succeeded', 'failed', 'refunded'])->default('succeeded');
            $table->string('method', 40)->nullable(); // card | upi | bank | manual
            $table->timestamp('paid_at')->nullable();
            $table->string('receipt_url', 500)->nullable();
            $table->foreignId('recorded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->index(['workspace_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_records');
        Schema::table('workspaces', function (Blueprint $table) {
            $table->dropColumn([
                'legal_name', 'contact_email', 'contact_phone',
                'address_line1', 'address_line2', 'address_city',
                'address_state', 'address_postal',
                'tax_type', 'tax_id', 'billing_notes',
            ]);
        });
    }
};
