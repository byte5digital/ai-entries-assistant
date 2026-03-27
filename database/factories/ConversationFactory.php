<?php

namespace Byte5\AiEntriesAssistant\Database\Factories;

use Byte5\AiEntriesAssistant\Models\Conversation;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Conversation>
 */
class ConversationFactory extends Factory
{
    protected $model = Conversation::class;

    public function definition(): array
    {
        return [
            'user_id' => (string) Str::uuid(),
            'title' => fake()->sentence(),
        ];
    }

    public function withoutTitle(): static
    {
        return $this->state(fn() => ['title' => null]);
    }
}
