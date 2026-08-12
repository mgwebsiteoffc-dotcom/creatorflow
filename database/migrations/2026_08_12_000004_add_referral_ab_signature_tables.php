<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── Step 9: contract e-signature ────────────────────────────────
        // Extend the existing contracts table with signature blobs + agree text.
        if (Schema::hasTable('contracts')) {
            Schema::table('contracts', function (Blueprint $table) {
                if (! Schema::hasColumn('contracts', 'creator_signature_name'))   $table->string('creator_signature_name')->nullable();
                if (! Schema::hasColumn('contracts', 'brand_signature_name'))     $table->string('brand_signature_name')->nullable();
                if (! Schema::hasColumn('contracts', 'creator_signature_svg'))    $table->longText('creator_signature_svg')->nullable();
                if (! Schema::hasColumn('contracts', 'brand_signature_svg'))      $table->longText('brand_signature_svg')->nullable();
                if (! Schema::hasColumn('contracts', 'brand_signature_ip'))       $table->string('brand_signature_ip', 45)->nullable();
                if (! Schema::hasColumn('contracts', 'creator_agreed_text'))      $table->text('creator_agreed_text')->nullable();
            });
        }

        // ── Step 11: A/B testing framework ──────────────────────────────
        Schema::create('ab_experiments', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('surface', 60)->default('landing');   // landing | hero | pricing | cta | signup
            $table->string('goal_event', 80)->nullable();        // 'signup_started', 'demo_booked', etc.
            $table->json('variants');                            // [{key:'A',weight:50,label,copy}, {key:'B',weight:50,...}]
            $table->enum('status', ['draft','running','paused','concluded'])->default('draft');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('ended_at')->nullable();
            $table->timestamps();
        });

        Schema::create('ab_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('experiment_id')->constrained('ab_experiments')->cascadeOnDelete();
            $table->string('variant_key', 20);
            $table->string('event', 40); // 'impression' | 'conversion'
            $table->string('visitor_id', 64)->nullable();
            $table->string('ip', 45)->nullable();
            $table->timestamp('created_at')->nullable();
            $table->index(['experiment_id', 'variant_key', 'event']);
        });

        // ── Step 12: referral / affiliate program ──────────────────────
        Schema::create('referral_codes', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('owner_type', 20); // 'creator' | 'user' | 'agency'
            $table->unsignedBigInteger('owner_id');
            $table->string('code', 40)->unique();
            $table->string('label')->nullable();
            $table->enum('kind', ['brand_referral','creator_affiliate','partner'])->default('brand_referral');
            $table->decimal('commission_rate', 5, 2)->default(20); // percentage on first invoice
            $table->integer('commission_fixed_cents')->default(0); // OR fixed cents per signup
            $table->integer('signup_count')->default(0);
            $table->integer('conversion_count')->default(0);
            $table->integer('lifetime_commission_cents')->default(0);
            $table->enum('status', ['active','disabled','expired'])->default('active');
            $table->timestamps();
            $table->index(['owner_type','owner_id']);
        });

        Schema::create('referral_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('referral_code_id')->constrained('referral_codes')->cascadeOnDelete();
            $table->foreignId('workspace_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('event', ['click','signup','activation','first_payment']);
            $table->integer('amount_cents')->default(0);
            $table->string('meta')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->index(['referral_code_id', 'event']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('referral_events');
        Schema::dropIfExists('referral_codes');
        Schema::dropIfExists('ab_events');
        Schema::dropIfExists('ab_experiments');
        if (Schema::hasTable('contracts')) {
            Schema::table('contracts', function (Blueprint $table) {
                foreach ([
                    'creator_signature_name','brand_signature_name',
                    'creator_signature_svg','brand_signature_svg',
                    'brand_signature_ip','creator_agreed_text',
                ] as $c) if (Schema::hasColumn('contracts', $c)) $table->dropColumn($c);
            });
        }
    }
};
