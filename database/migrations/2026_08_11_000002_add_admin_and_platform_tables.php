<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // System-level role + suspension on users (owner of the platform).
        Schema::table('users', function (Blueprint $table) {
            $table->string('system_role', 20)->default('user')->after('email'); // user | admin | superadmin
            $table->string('account_status', 20)->default('active')->after('system_role'); // active | suspended
            $table->text('suspension_reason')->nullable()->after('account_status');
            $table->timestamp('suspended_at')->nullable()->after('suspension_reason');
            $table->index('system_role');
            $table->index('account_status');
        });

        // Ban / suspend workspaces + creators from admin.
        Schema::table('workspaces', function (Blueprint $table) {
            $table->string('account_status', 20)->default('active')->after('plan_status');
            $table->text('suspension_reason')->nullable()->after('account_status');
            $table->timestamp('suspended_at')->nullable()->after('suspension_reason');
        });

        Schema::table('creators', function (Blueprint $table) {
            $table->text('suspension_reason')->nullable()->after('status');
            $table->timestamp('suspended_at')->nullable()->after('suspension_reason');
        });

        // Platform-wide settings (single-row config).
        Schema::create('platform_settings', function (Blueprint $table) {
            $table->id();
            $table->decimal('paid_platform_fee_rate', 5, 4)->default(0.1000);      // 10%
            $table->decimal('barter_platform_fee_rate', 5, 4)->default(0.0500);    // 5%
            $table->decimal('processing_markup_rate', 5, 4)->default(0.0290);      // 2.9%
            $table->integer('processing_markup_fixed_cents')->default(30);         // + 30¢
            $table->integer('escrow_hold_days')->default(7);
            $table->integer('minimum_payout_cents')->default(1000);
            $table->boolean('require_creator_verification')->default(true);
            $table->boolean('allow_public_signup')->default(true);
            $table->json('metadata')->nullable();
            $table->timestamps();
        });

        // Escrow ledger — every hold, release, refund.
        Schema::create('escrow_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workspace_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('creator_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('assignment_id')->nullable()->constrained('campaign_assignments')->nullOnDelete();
            $table->foreignId('payout_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('kind', ['hold', 'release', 'refund', 'fee', 'markup', 'adjustment']);
            $table->integer('amount_cents');                    // signed: + hold/inflow, - release/outflow
            $table->char('currency', 3)->default('USD');
            $table->string('reference')->nullable();
            $table->text('note')->nullable();
            $table->foreignId('performed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->index(['workspace_id', 'created_at']);
            $table->index(['creator_id', 'created_at']);
        });

        // Bulk creator imports.
        Schema::create('creator_imports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('source', 40)->default('csv'); // csv | api | manual
            $table->string('filename')->nullable();
            $table->integer('total_rows')->default(0);
            $table->integer('imported_rows')->default(0);
            $table->integer('failed_rows')->default(0);
            $table->json('errors')->nullable();
            $table->string('status', 20)->default('completed');
            $table->timestamps();
        });

        // Leads from marketing site / contact form.
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->string('company')->nullable();
            $table->string('reason', 40)->nullable();         // demo | pricing | partnership | other
            $table->text('message')->nullable();
            $table->string('source', 60)->default('contact_form');
            $table->string('utm_source')->nullable();
            $table->string('utm_campaign')->nullable();
            $table->string('utm_medium')->nullable();
            $table->ipAddress('ip')->nullable();
            $table->string('user_agent', 500)->nullable();
            $table->enum('status', ['new', 'contacted', 'qualified', 'won', 'lost'])->default('new');
            $table->text('note')->nullable();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->index('status');
            $table->index('created_at');
        });

        // Blog posts stored in DB (SEO-friendly, with meta + JSON-LD).
        Schema::create('blog_posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('author_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('slug')->unique();
            $table->string('title');
            $table->string('category', 60)->nullable();
            $table->text('excerpt')->nullable();
            $table->longText('body');                         // Markdown
            $table->string('cover_gradient', 120)->default('from-violet-500 to-pink-500');
            $table->string('cover_image_path')->nullable();
            $table->string('meta_title')->nullable();
            $table->string('meta_description', 300)->nullable();
            $table->string('canonical_url')->nullable();
            $table->json('faq_json')->nullable();             // [{q,a}, ...] — rendered as JSON-LD FAQPage
            $table->string('read_minutes', 12)->nullable();   // '7 min'
            $table->boolean('is_published')->default(true);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
            $table->index(['is_published', 'published_at']);
        });

        Schema::table('blog_posts', function (Blueprint $table) {
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blog_posts');
        Schema::dropIfExists('leads');
        Schema::dropIfExists('creator_imports');
        Schema::dropIfExists('escrow_transactions');
        Schema::dropIfExists('platform_settings');

        Schema::table('creators', function (Blueprint $table) {
            $table->dropColumn(['suspension_reason', 'suspended_at']);
        });
        Schema::table('workspaces', function (Blueprint $table) {
            $table->dropColumn(['account_status', 'suspension_reason', 'suspended_at']);
        });
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['system_role']);
            $table->dropIndex(['account_status']);
            $table->dropColumn(['system_role', 'account_status', 'suspension_reason', 'suspended_at']);
        });
    }
};
