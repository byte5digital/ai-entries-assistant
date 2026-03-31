<?php

declare(strict_types=1);

namespace Byte5\AiEntriesAssistant\Services;

use Byte5\AiEntriesAssistant\Events\ConversationDeleted;
use Byte5\AiEntriesAssistant\Events\ConversationStarted;
use Byte5\AiEntriesAssistant\Models\Conversation;
use Byte5\AiEntriesAssistant\Services\Contracts\ConversationServiceInterface;
use Byte5\AiEntriesAssistant\Services\Contracts\MessageServiceInterface;
use Illuminate\Support\Str;

final class ConversationService implements ConversationServiceInterface
{
    public function __construct(
        private readonly MessageServiceInterface $messageService,
    ) {
    }

    public function startConversation(string $content, string $userId): Conversation
    {
        $conversation = Conversation::create([
            'user_id' => $userId,
            'title' => Str::limit($content, 100, preserveWords: true),
        ]);


        $this->messageService->createUserMessage($conversation->id, $userId, $content);

        ConversationStarted::dispatch($conversation, $userId);

        return $conversation;
    }

    public function deleteConversation(Conversation $conversation): void
    {
        $conversationId = $conversation->id;
        $userId = $conversation->user_id;
        $title = $conversation->title;

        $conversation->delete();

        ConversationDeleted::dispatch($conversationId, $userId, $title);
    }
}
