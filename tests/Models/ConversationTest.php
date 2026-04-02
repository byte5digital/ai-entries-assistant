<?php

declare(strict_types=1);

namespace Byte5\AiEntriesAssistant\Tests\Models;

use Byte5\AiEntriesAssistant\Models\Conversation;
use Byte5\AiEntriesAssistant\Models\Message;
use Byte5\AiEntriesAssistant\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

final class ConversationTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_can_be_created_with_factory(): void
    {
        $conversation = Conversation::factory()->create();

        $this->assertDatabaseHas('ai_entry_assistant_conversations', [
            'id' => $conversation->id,
        ]);
    }

    public function test_it_has_messages_relationship(): void
    {
        $conversation = Conversation::factory()->create();
        Message::factory()->for($conversation)->count(3)->create();

        $this->assertCount(3, $conversation->messages);
    }

    public function test_it_has_latest_message_relationship(): void
    {
        $conversation = Conversation::factory()->create();

        Message::factory()->for($conversation)->create([
            'content' => 'First message',
            'created_at' => now()->subMinute(),
        ]);
        Message::factory()->for($conversation)->create([
            'content' => 'Latest message',
            'created_at' => now(),
        ]);

        $this->assertSame('Latest message', $conversation->latestMessage->content);
    }

    public function test_it_generates_short_title(): void
    {
        $conversation = Conversation::factory()->create([
            'title' => 'This is a really long conversation title that should be truncated',
        ]);

        $this->assertStringStartsWith('This is a really lon...', $conversation->short_title);
    }

    public function test_short_title_handles_null_title(): void
    {
        $conversation = Conversation::factory()->withoutTitle()->create();

        $this->assertSame('', $conversation->short_title);
    }

    public function test_for_user_scope_filters_by_user_id(): void
    {
        $userId = 'user-1';
        Conversation::factory()->create(['user_id' => $userId]);
        Conversation::factory()->create(['user_id' => $userId]);
        Conversation::factory()->create(['user_id' => 'user-2']);

        $conversations = Conversation::query()->forUser($userId)->get();

        $this->assertCount(2, $conversations);
    }


}
