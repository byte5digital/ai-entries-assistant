<?php

declare(strict_types=1);

namespace Byte5\AiEntriesAssistant\Tests\Models;

use Byte5\AiEntriesAssistant\Models\Conversation;
use Byte5\AiEntriesAssistant\Models\Message;
use Byte5\AiEntriesAssistant\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

final class MessageTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_messages_scope(): void
    {
        $conversation = Conversation::factory()->create();
        Message::factory()->for($conversation)->fromUser()->count(2)->create();
        Message::factory()->for($conversation)->fromAiAssistant()->create();

        $userMessages = Message::query()->userMessages()->get();

        $this->assertCount(2, $userMessages);
    }

    public function test_assistant_messages_scope(): void
    {
        $conversation = Conversation::factory()->create();
        Message::factory()->for($conversation)->fromUser()->create();
        Message::factory()->for($conversation)->fromAiAssistant()->count(2)->create();

        $assistantMessages = Message::query()->assistantMessages()->get();

        $this->assertCount(2, $assistantMessages);
    }
}
