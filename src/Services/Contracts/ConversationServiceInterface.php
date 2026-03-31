<?php

declare(strict_types=1);

namespace Byte5\AiEntriesAssistant\Services\Contracts;

use Byte5\AiEntriesAssistant\Models\Conversation;

/**
 * Manages conversation lifecycle.
 *
 * Starting a conversation creates the conversation record, persists the
 * first user message, and dispatches {@see \Byte5\AiEntriesAssistant\Events\ConversationStarted}.
 * The AI assistant reply is generated asynchronously by a queued listener
 * that reacts to the {@see \Byte5\AiEntriesAssistant\Events\UserMessageAddedToConversation} event.
 */
interface ConversationServiceInterface
{
    /**
     * Create a new conversation with the given first message.
     *
     * The conversation title is derived from the message content.
     * Dispatches {@see \Byte5\AiEntriesAssistant\Events\ConversationStarted}
     * after creation.
     */
    public function startConversation(string $content, string $userId): Conversation;

    /**
     * Delete a conversation and all its messages.
     *
     * Dispatches {@see \Byte5\AiEntriesAssistant\Events\ConversationDeleted}
     * after removal.
     */
    public function deleteConversation(Conversation $conversation): void;
}
