<?php

declare(strict_types=1);

namespace Byte5\AiEntriesAssistant\Services\Contracts;

use Byte5\AiEntriesAssistant\Models\Message;
use Illuminate\Support\Collection;

interface MessageServiceInterface
{
    public function createUserMessage(string $conversationId, string $userId, string $content): Message;

    public function createAiAssistantMessage(string $conversationId, string $content): Message;

    /** @return Collection<int, Message> */
    public function getMessages(string $conversationId, ?int $limit = null): Collection;
}
