<?php

declare(strict_types=1);

namespace Byte5\AiEntriesAssistant\Services;

use Byte5\AiEntriesAssistant\Enums\MessageRole;
use Byte5\AiEntriesAssistant\Events\AiAssistantMessageAddedToConversation;
use Byte5\AiEntriesAssistant\Events\UserMessageAddedToConversation;
use Byte5\AiEntriesAssistant\Models\Conversation;
use Byte5\AiEntriesAssistant\Models\Message;
use Byte5\AiEntriesAssistant\Services\Contracts\MessageServiceInterface;
use Illuminate\Support\Collection;

final class MessageService implements MessageServiceInterface
{
    /** @return Collection<int, Message> */
    public function getMessages(string $conversationId, ?int $limit = null): Collection
    {
        return Message::query()->where('conversation_id', $conversationId)->oldest()
            ->when($limit, fn ($query) => $query->limit($limit))
            ->get();
    }

    public function createUserMessage(string $conversationId, string $userId, string $content): Message
    {
        $message = Message::query()->create([
            'conversation_id' => $conversationId,
            'user_id' => $userId,
            'role' => MessageRole::User,
            'content' => $content,
        ]);

        $conversation = Conversation::query()->findOrFail($conversationId);
        event(new UserMessageAddedToConversation($message, $conversation));

        return $message;
    }

    public function createAiAssistantMessage(string $conversationId, string $content): Message
    {
        $message = Message::query()->create([
            'conversation_id' => $conversationId,
            'role' => MessageRole::AiAssistant,
            'content' => $content,
        ]);

        $conversation = Conversation::query()->findOrFail($conversationId);
        event(new AiAssistantMessageAddedToConversation($message, $conversation));

        return $message;
    }
}
