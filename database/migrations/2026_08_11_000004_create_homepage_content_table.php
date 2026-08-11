<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('homepage_items', function (Blueprint $table) {
            $table->id();
            // Section: 'client_logo' | 'sample_reel' | 'hero_image'
            $table->string('section', 40);
            $table->string('category', 60)->nullable();      // 'Beauty', 'Food', 'Travel'…
            $table->string('title', 190)->nullable();
            $table->string('subtitle', 190)->nullable();     // e.g. creator handle
            $table->string('image_path', 500)->nullable();
            $table->string('video_path', 500)->nullable();
            $table->string('poster_path', 500)->nullable();  // reel thumbnail
            $table->string('external_url', 800)->nullable();
            $table->string('gradient', 120)->nullable();     // e.g. 'from-amber-400 to-orange-500'
            $table->string('meta', 190)->nullable();         // free-form: '2.4M views'
            $table->integer('position')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->index(['section', 'is_active', 'position']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('homepage_items');
    }
};
