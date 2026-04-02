<?php

declare(strict_types=1);

namespace Byte5\AiEntriesAssistant\Repositories\Contracts;

use Byte5\AiEntriesAssistant\Models\Conversation;
use Illuminate\Support\Collection;

interface ConversationRepositoryInterface
{
    /**
     * Get all conversations for the given user.
     *
     * @return Collection<int, Conversation>
     */
    public function userConversations(int|string $userIdentifier): Collection;

    /**
     * Get the last conversation for the given user.
     */
    public function getLastConversation(int|string $userIdentifier): ?Conversation;
}
