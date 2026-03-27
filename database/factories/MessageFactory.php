<?php

namespace Byte5\AiEntriesAssistant\Database\Factories;

use Byte5\AiEntriesAssistant\Enums\MessageRole;
use Byte5\AiEntriesAssistant\Models\Conversation;
use Byte5\AiEntriesAssistant\Models\Message;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Message>
 */
class MessageFactory extends Factory
{
    protected $model = Message::class;

    public function definition(): array
    {
        return [
            'conversation_id' => Conversation::factory(),
            'user_id' => (string) Str::uuid(),
            'role' => MessageRole::User,
            'content' => fake()->paragraph(),
        ];
    }

    public function fromUser(): static
    {
        return $this->state(fn() => ['role' => MessageRole::User]);
    }

    public function fromAiAssistant(): static
    {
        return $this->state(fn() => ['role' => MessageRole::AiAssistant]);
    }
}
