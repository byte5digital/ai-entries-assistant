<?php

declare(strict_types=1);

namespace Byte5\AiEntriesAssistant\Tests\Services;

use Byte5\AiEntriesAssistant\Events\ConversationDeleted;
use Byte5\AiEntriesAssistant\Events\ConversationStarted;
use Byte5\AiEntriesAssistant\Events\UserMessageAddedToConversation;
use Byte5\AiEntriesAssistant\Models\Conversation;
use Byte5\AiEntriesAssistant\Models\Message;
use Byte5\AiEntriesAssistant\Services\ConversationService;
use Byte5\AiEntriesAssistant\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;

final class ConversationServiceTest extends TestCase
{
    use RefreshDatabase;

    private ConversationService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = $this->app->make(ConversationService::class);
    }

    public function test_start_conversation_creates_conversation_and_message(): void
    {
        Event::fake([ConversationStarted::class, UserMessageAddedToConversation::class]);

        $conversation = $this->service->startConversation('Hello, AI!', 'user-123');

        $this->assertDatabaseHas('ai_entry_assistant_conversations', [
            'id' => $conversation->id,
            'user_id' => 'user-123',
        ]);

        $this->assertDatabaseHas('ai_entry_assistant_messages', [
            'conversation_id' => $conversation->id,
            'content' => 'Hello, AI!',
            'role' => 'user',
        ]);
    }

    public function test_start_conversation_generates_title_from_content(): void
    {
        Event::fake([ConversationStarted::class, UserMessageAddedToConversation::class]);

        $conversation = $this->service->startConversation('Hello, AI!', 'user-123');

        $this->assertSame('Hello, AI!', $conversation->title);
    }

    public function test_start_conversation_truncates_long_title(): void
    {
        Event::fake([ConversationStarted::class, UserMessageAddedToConversation::class]);

        $longContent = str_repeat('a ', 200);
        $conversation = $this->service->startConversation($longContent, 'user-123');

        $this->assertLessThanOrEqual(103, strlen($conversation->title)); // 100 + "..."
    }

    public function test_start_conversation_dispatches_events(): void
    {
        Event::fake([ConversationStarted::class, UserMessageAddedToConversation::class]);

        $this->service->startConversation('Hello!', 'user-123');

        Event::assertDispatched(ConversationStarted::class);
        Event::assertDispatched(UserMessageAddedToConversation::class);
    }

    public function test_delete_conversation_removes_it(): void
    {
        Event::fake([ConversationDeleted::class]);

        $conversation = Conversation::factory()->create();

        $this->service->deleteConversation($conversation);

        $this->assertDatabaseMissing('ai_entry_assistant_conversations', [
            'id' => $conversation->id,
        ]);
    }

    public function test_delete_conversation_dispatches_event(): void
    {
        Event::fake([ConversationDeleted::class]);

        $conversation = Conversation::factory()->create();

        $this->service->deleteConversation($conversation);

        Event::assertDispatched(ConversationDeleted::class, function (ConversationDeleted $event) use ($conversation): bool {
            return $event->conversationId === $conversation->id
                && $event->userId === $conversation->user_id;
        });
    }

    public function test_update_title(): void
    {
        $conversation = Conversation::factory()->create(['title' => 'Old title']);

        $updated = $this->service->updateTitle($conversation, 'New title');

        $this->assertSame('New title', $updated->title);
        $this->assertDatabaseHas('ai_entry_assistant_conversations', [
            'id' => $conversation->id,
            'title' => 'New title',
        ]);
    }
}
