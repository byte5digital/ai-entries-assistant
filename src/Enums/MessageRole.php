<?php

declare(strict_types=1);

namespace Byte5\AiEntriesAssistant\Enums;

enum MessageRole: string
{
    case User = 'user';
    case AiAssistant = 'ai_assistant';
}
