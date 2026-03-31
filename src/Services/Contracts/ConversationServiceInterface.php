<?php

declare(strict_types=1);

namespace Byte5\AiEntriesAssistant\Services\Contracts;

use Byte5\AiEntriesAssistant\Models\Conversation;

interface ConversationServiceInterface
{
    public function startConversation(string $content, string $userId): Conversation;

    public function deleteConversation(Conversation $conversation): void;
}
