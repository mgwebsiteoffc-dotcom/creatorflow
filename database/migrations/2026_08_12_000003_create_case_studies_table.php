<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('case_studies', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('workspace_id')->nullable()->constrained()->nullOnDelete();
            $table->string('slug')->unique();
            $table->string('brand_name');
            $table->string('brand_logo_path')->nullable();
            $table->string('cover_image_path')->nullable();
            $table->string('hero_video_url')->nullable();
            $table->string('industry', 80)->nullable();
            $table->string('city', 80)->nullable();
            $table->string('campaign_type', 40)->nullable();      // barter | paid | ugc | hybrid
            $table->string('headline');
            $table->string('subheadline', 500)->nullable();
            $table->longText('summary')->nullable();              // markdown
            $table->longText('challenge')->nullable();            // markdown
            $table->longText('solution')->nullable();             // markdown
            $table->longText('results')->nullable();              // markdown
            $table->json('metrics')->nullable();                  // [{label,value,tone}]
            $table->json('gallery')->nullable();                  // paths
            $table->string('quote', 800)->nullable();
            $table->string('quote_author')->nullable();
            $table->string('quote_role')->nullable();
            $table->json('seo')->nullable();                      // {meta_title, meta_description, og_image}
            $table->timestamp('published_at')->nullable();
            $table->boolean('featured')->default(false);
            $table->integer('position')->default(0);
            $table->timestamps();
            $table->softDeletes();
            $table->index(['published_at', 'featured', 'position']);
        });
    }
    public function down(): void { Schema::dropIfExists('case_studies'); }
};
