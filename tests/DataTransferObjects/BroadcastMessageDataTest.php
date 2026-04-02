<?php

declare(strict_types=1);

namespace Byte5\AiEntriesAssistant\Tests\DataTransferObjects;

use Byte5\AiEntriesAssistant\DataTransferObjects\BroadcastMessageData;
use Byte5\AiEntriesAssistant\Models\Message;
use Byte5\AiEntriesAssistant\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

final class BroadcastMessageDataTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_from_message(): void
    {
        $message = Message::factory()->fromUser()->create([
            'content' => 'Test message',
        ]);

        $data = BroadcastMessageData::fromMessage($message);

        $this->assertSame($message->id, $data->id);
        $this->assertSame($message->conversation_id, $data->conversation_id);
        $this->assertSame('user', $data->role);
        $this->assertSame('Test message', $data->content);
    }

    public function test_to_array_returns_all_fields(): void
    {
        $message = Message::factory()->create();

        $data = BroadcastMessageData::fromMessage($message);
        $array = $data->toArray();

        $this->assertArrayHasKey('id', $array);
        $this->assertArrayHasKey('conversation_id', $array);
        $this->assertArrayHasKey('role', $array);
        $this->assertArrayHasKey('content', $array);
        $this->assertArrayHasKey('created_at', $array);
    }
}
