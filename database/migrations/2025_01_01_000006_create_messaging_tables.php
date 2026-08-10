<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('message_threads', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('workspace_id')->constrained()->cascadeOnDelete();
            $table->foreignId('campaign_id')->nullable()->constrained()->nullOnDelete();
            $table->string('subject')->nullable();
            $table->timestamps();
            $table->index('workspace_id');
        });

        Schema::create('message_thread_participants', function (Blueprint $table) {
            $table->foreignId('thread_id')->constrained('message_threads')->cascadeOnDelete();
            $table->enum('participant_type', ['user', 'creator']);
            $table->unsignedBigInteger('participant_id');
            $table->timestamp('last_read_at')->nullable();
            $table->boolean('muted')->default(false);
            $table->primary(['thread_id', 'participant_type', 'participant_id'], 'mtp_primary');
            $table->index(['participant_type', 'participant_id']);
        });

        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('thread_id')->constrained('message_threads')->cascadeOnDelete();
            $table->enum('sender_type', ['user', 'creator', 'system', 'ai']);
            $table->unsignedBigInteger('sender_id')->nullable();
            $table->text('body');
            $table->json('attachments')->nullable();
            $table->boolean('ai_generated')->default(false);
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
            $table->index(['thread_id', 'created_at']);
        });

        Schema::create('notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->enum('recipient_type', ['user', 'creator']);
            $table->unsignedBigInteger('recipient_id');
            $table->string('type', 120);
            $table->json('data');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
            $table->index(['recipient_type', 'recipient_id', 'read_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('messages');
        Schema::dropIfExists('message_thread_participants');
        Schema::dropIfExists('message_threads');
    }
};
