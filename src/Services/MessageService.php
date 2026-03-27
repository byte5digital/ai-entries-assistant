<?php

declare(strict_types=1);

namespace Byte5\AiEntriesAssistant\Services;

use Byte5\AiEntriesAssistant\Enums\MessageRole;
use Byte5\AiEntriesAssistant\Models\Message;
use Byte5\AiEntriesAssistant\Services\Contracts\MessageServiceInterface;

final class MessageService implements MessageServiceInterface
{
    public function createUserMessage(string $conversationId, string $userId, string $content): Message
    {
        return Message::create([
            'conversation_id' => $conversationId,
            'user_id' => $userId,
            'role' => MessageRole::User,
            'content' => $content,
        ]);
    }
}
