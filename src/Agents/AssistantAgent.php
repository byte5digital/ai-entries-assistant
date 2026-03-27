<?php

declare(strict_types=1);

namespace Byte5\AiEntriesAssistant\Agents;

use Laravel\Ai\Concerns\RemembersConversations;
use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Contracts\Conversational;
use Laravel\Ai\Promptable;
use Stringable;

final class AssistantAgent implements Agent, Conversational
{
    use Promptable;
    use RemembersConversations;

    public function instructions(): Stringable|string
    {
        return config('ai-entries-assistant.agent.instructions', 'You are a helpful assistant.');
    }

    public function provider(): ?string
    {
        return config('ai-entries-assistant.agent.provider');
    }

    public function model(): ?string
    {
        return config('ai-entries-assistant.agent.model');
    }

    protected function maxConversationMessages(): int
    {
        return (int) config('ai-entries-assistant.agent.max_conversation_messages', 100);
    }
}
