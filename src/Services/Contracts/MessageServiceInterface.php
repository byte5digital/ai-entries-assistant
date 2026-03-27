<?php

declare(strict_types=1);

namespace Byte5\AiEntriesAssistant\Services\Contracts;

use Byte5\AiEntriesAssistant\Models\Message;

interface MessageServiceInterface
{
    public function createUserMessage(string $conversationId, string $userId, string $content): Message;
}
