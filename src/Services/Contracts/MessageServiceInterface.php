<?php

declare(strict_types=1);

namespace Byte5\AiEntriesAssistant\Services\Contracts;

use Byte5\AiEntriesAssistant\Models\Message;
use Illuminate\Support\Collection;

/**
 * Handles message persistence and retrieval within conversations.
 *
 * User messages are stored synchronously and trigger the
 * {@see \Byte5\AiEntriesAssistant\Events\UserMessageAddedToConversation} event,
 * which is picked up by a queued listener
 * ({@see \Byte5\AiEntriesAssistant\Listeners\GenerateAiAssistantReply})
 * to generate the AI assistant response asynchronously.
 */
interface MessageServiceInterface
{
    /**
     * Persist a user message and dispatch the event that triggers AI processing.
     *
     * The AI response is NOT generated here — it happens asynchronously
     * via the {@see \Byte5\AiEntriesAssistant\Listeners\GenerateAiAssistantReply}
     * queued listener.
     */
    public function createUserMessage(string $conversationId, string $userId, string $content): Message;

    /**
     * Persist an AI assistant message.
     *
     * Typically called by the queued listener after the agent produces a response.
     * Dispatches {@see \Byte5\AiEntriesAssistant\Events\AiAssistantMessageAddedToConversation}.
     */
    public function createAiAssistantMessage(string $conversationId, string $content): Message;

    /**
     * Retrieve messages for a conversation, ordered chronologically.
     *
     * @param  int|null  $limit  Maximum number of messages to return (null = all).
     * @return Collection<int, Message>
     */
    public function getMessages(string $conversationId, ?int $limit = null): Collection;
}
