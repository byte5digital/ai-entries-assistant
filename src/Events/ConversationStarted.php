<?php

declare(strict_types=1);

namespace Byte5\AiEntriesAssistant\Events;

use Byte5\AiEntriesAssistant\Models\Conversation;
use Illuminate\Foundation\Events\Dispatchable;

final readonly class ConversationStarted
{
    use Dispatchable;

    public function __construct(
        public Conversation $conversation,
        public string $userId,
    ) {}
}
