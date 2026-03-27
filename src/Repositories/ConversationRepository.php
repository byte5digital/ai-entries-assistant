<?php

declare(strict_types=1);

namespace Byte5\AiEntriesAssistant\Repositories;

use Byte5\AiEntriesAssistant\Models\Conversation;
use Byte5\AiEntriesAssistant\Repositories\Contracts\ConversationRepositoryInterface;
use Illuminate\Support\Collection;
use Statamic\Facades\User;

final class ConversationRepository implements ConversationRepositoryInterface
{
    /** @inheritDoc */
    public function userConversations(int|string $userIdentifier): Collection
    {
        return Conversation::query()->forUser((string) $userIdentifier)->get();
    }

    /** @inheritDoc */
    public function getLastConversation(int|string $userIdentifier): ?Conversation
    {
        return Conversation::query()->forUser((string) $userIdentifier)->latest()->first();
    }
}
