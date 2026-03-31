<?php

declare(strict_types=1);

namespace Byte5\AiEntriesAssistant\DataTransferObjects;

use Byte5\AiEntriesAssistant\Models\Message;

final readonly class BroadcastMessageData
{
    public function __construct(
        public string $id,
        public string $conversation_id,
        public string $role,
        public string $content,
        public string $created_at,
    ) {}

    public static function fromMessage(Message $message): self
    {
        return new self(
            id: $message->id,
            conversation_id: $message->conversation_id,
            role: $message->role->value,
            content: $message->content,
            created_at: $message->created_at->toISOString(),
        );
    }

    /** @return array<string, string> */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'conversation_id' => $this->conversation_id,
            'role' => $this->role,
            'content' => $this->content,
            'created_at' => $this->created_at,
        ];
    }
}
