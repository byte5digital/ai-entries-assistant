<?php

declare(strict_types=1);

namespace Byte5\AiEntriesAssistant\Support;

/**
 * Wraps a user ID as a public property for laravel/ai compatibility.
 *
 * The RememberConversation middleware accesses ->id as a property,
 * but Statamic users expose id() as a method. This DTO bridges that gap.
 */
final readonly class ConversationUser
{
    public function __construct(public string $id) {}
}
