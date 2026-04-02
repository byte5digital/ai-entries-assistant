<?php

declare(strict_types=1);

namespace Byte5\AiEntriesAssistant\Tests\Services;

use Byte5\AiEntriesAssistant\Enums\MessageRole;
use Byte5\AiEntriesAssistant\Events\AiAssistantMessageAddedToConversation;
use Byte5\AiEntriesAssistant\Events\UserMessageAddedToConversation;
use Byte5\AiEntriesAssistant\Models\Conversation;
use Byte5\AiEntriesAssistant\Models\Message;
use Byte5\AiEntriesAssistant\Services\MessageService;
use Byte5\AiEntriesAssistant\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;

final class MessageServiceTest extends TestCase
{
    use RefreshDatabase;

    private MessageService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = $this->app->make(MessageService::class);
    }

    public function test_create_user_message(): void
    {
        Event::fake([UserMessageAddedToConversation::class]);

        $conversation = Conversation::factory()->create();

        $message = $this->service->createUserMessage($conversation->id, 'user-123', 'Hello!');

        $this->assertSame(MessageRole::User, $message->role);
        $this->assertSame('Hello!', $message->content);
        $this->assertSame($conversation->id, $message->conversation_id);
    }

    public function test_create_user_message_dispatches_event(): void
    {
        Event::fake([UserMessageAddedToConversation::class]);

        $conversation = Conversation::factory()->create();

        $this->service->createUserMessage($conversation->id, 'user-123', 'Hello!');

        Event::assertDispatched(UserMessageAddedToConversation::class);
    }

    public function test_create_ai_assistant_message(): void
    {
        Event::fake([AiAssistantMessageAddedToConversation::class]);

        $conversation = Conversation::factory()->create();

        $message = $this->service->createAiAssistantMessage($conversation->id, 'AI response');

        $this->assertSame(MessageRole::AiAssistant, $message->role);
        $this->assertSame('AI response', $message->content);
    }

    public function test_create_ai_assistant_message_dispatches_event(): void
    {
        Event::fake([AiAssistantMessageAddedToConversation::class]);

        $conversation = Conversation::factory()->create();

        $this->service->createAiAssistantMessage($conversation->id, 'AI response');

        Event::assertDispatched(AiAssistantMessageAddedToConversation::class);
    }

    public function test_get_messages_returns_chronological_order(): void
    {
        $conversation = Conversation::factory()->create();

        Message::factory()->for($conversation)->create([
            'content' => 'Second',
            'created_at' => now(),
        ]);
        Message::factory()->for($conversation)->create([
            'content' => 'First',
            'created_at' => now()->subMinute(),
        ]);

        $messages = $this->service->getMessages($conversation->id);

        $this->assertSame('First', $messages->first()->content);
        $this->assertSame('Second', $messages->last()->content);
    }

    public function test_get_messages_respects_limit(): void
    {
        $conversation = Conversation::factory()->create();
        Message::factory()->for($conversation)->count(5)->create();

        $messages = $this->service->getMessages($conversation->id, limit: 3);

        $this->assertCount(3, $messages);
    }

    public function test_get_messages_returns_empty_collection_for_no_messages(): void
    {
        $conversation = Conversation::factory()->create();

        $messages = $this->service->getMessages($conversation->id);

        $this->assertCount(0, $messages);
    }
}
