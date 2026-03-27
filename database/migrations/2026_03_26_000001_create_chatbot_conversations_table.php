<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Alter user_id from unsignedBigInteger to string for Statamic UUID compatibility
        if (Schema::hasTable('agent_conversations')) {
            Schema::table('agent_conversations', function (Blueprint $table) {
                $table->string('user_id', 36)->nullable()->change();
            });
        }

        if (Schema::hasTable('agent_conversation_messages')) {
            Schema::table('agent_conversation_messages', function (Blueprint $table) {
                $table->string('user_id', 36)->nullable()->change();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('agent_conversations')) {
            Schema::table('agent_conversations', function (Blueprint $table) {
                $table->unsignedBigInteger('user_id')->nullable()->change();
            });
        }

        if (Schema::hasTable('agent_conversation_messages')) {
            Schema::table('agent_conversation_messages', function (Blueprint $table) {
                $table->unsignedBigInteger('user_id')->nullable()->change();
            });
        }
    }
};
