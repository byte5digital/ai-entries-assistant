<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ai_entry_assistant_conversations', function (Blueprint $table): void {
            $table->uuid('id')->primary()->comment('Unique conversation identifier');
            $table->string('user_id', 36)->index()->comment('Statamic user UUID');
            $table->string('title')->nullable()->comment('Conversation title, generated from first message');
            $table->timestamps();
        });

        Schema::create('ai_entry_assistant_messages', function (Blueprint $table): void {
            $table->uuid('id')->primary()->comment('Unique message identifier');
            $table->uuid('conversation_id')->comment('Parent conversation reference');
            $table->foreign('conversation_id')
                ->references('id')
                ->on('ai_entry_assistant_conversations')
                ->cascadeOnDelete();
            $table->string('user_id', 36)->nullable()->index()->comment('Statamic user UUID of message author, null for AI messages');
            $table->string('role')->comment('Message role: user or ai_assistant');
            $table->text('content')->comment('Message body text');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ai_entry_assistant_messages');
        Schema::dropIfExists('ai_entry_assistant_conversations');
    }
};
