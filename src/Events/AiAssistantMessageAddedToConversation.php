<?php

declare(strict_types=1);

namespace Byte5\AiEntriesAssistant\Events;

use Byte5\AiEntriesAssistant\Models\Conversation;
use Byte5\AiEntriesAssistant\Models\Message;
use Illuminate\Foundation\Events\Dispatchable;

final readonly class AiAssistantMessageAddedToConversation
{
    use Dispatchable;

    public function __construct(
        public Message $message,
        public Conversation $conversation,
    ) {
    }
}