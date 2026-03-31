<?php

declare(strict_types=1);

namespace Byte5\AiEntriesAssistant\Events;

use Byte5\AiEntriesAssistant\DataTransferObjects\BroadcastMessageData;
use Byte5\AiEntriesAssistant\Models\Conversation;
use Byte5\AiEntriesAssistant\Models\Message;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

final class UserMessageAddedToConversation implements ShouldBroadcast
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public function __construct(
        public readonly Message $message,
        public readonly Conversation $conversation,
    ) {}

    /** @return array<int, PrivateChannel> */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('conversation.'.$this->conversation->id),
        ];
    }

    public function broadcastAs(): string
    {
        return 'user.message.created';
    }

    /** @return array<string, mixed> */
    public function broadcastWith(): array
    {
        return [
            'message' => BroadcastMessageData::fromMessage($this->message)->toArray(),
        ];
    }

    public function broadcastQueue(): string
    {
        return config('ai-entries-assistant.broadcasting_queue', 'default');
    }
}
