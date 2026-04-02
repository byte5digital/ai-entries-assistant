<?php

declare(strict_types=1);

namespace Byte5\AiEntriesAssistant\Events;

use Illuminate\Foundation\Events\Dispatchable;

final readonly class ConversationDeleted
{
    use Dispatchable;

    public function __construct(
        public string $conversationId,
        public string $userId,
        public ?string $title,
    ) {}
}
