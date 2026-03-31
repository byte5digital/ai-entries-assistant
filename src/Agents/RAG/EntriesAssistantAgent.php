<?php

declare(strict_types=1);

namespace Byte5\AiEntriesAssistant\Agents\RAG;

use Byte5\AiEntriesAssistant\Enums\MessageRole;
use Byte5\AiEntriesAssistant\Models\Message;
use Byte5\AiEntriesAssistant\Services\Contracts\MessageServiceInterface;
use Byte5\AiEntryEmbeddings\Models\EntryEmbeddingChunk;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Attributes\Temperature;
use Laravel\Ai\Attributes\Timeout;
use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Contracts\Conversational;
use Laravel\Ai\Contracts\HasStructuredOutput;
use Laravel\Ai\Contracts\HasTools;
use Laravel\Ai\Enums\Lab;
use Laravel\Ai\Messages\AssistantMessage;
use Laravel\Ai\Messages\UserMessage;
use Laravel\Ai\Promptable;
use Laravel\Ai\Tools\SimilaritySearch;
use Stringable;

#[Temperature(0.7)]
#[Timeout(120)]
final class EntriesAssistantAgent implements Agent, Conversational, HasTools, HasStructuredOutput
{
    use Promptable;

    protected ?string $conversationId = null;

    public function __construct(
        private readonly MessageServiceInterface $messageService,
    ) {
    }

    public function provider(): Lab|string
    {
        return config('ai-entries-assistant.provider') ?? config('ai.default');
    }

    public function forConversation(string $conversationId): self
    {
        $this->conversationId = $conversationId;

        return $this;
    }

    public function instructions(): Stringable|string
    {
        return <<<'INSTRUCTIONS'
        You are a helpful assistant that answers questions based exclusively on information found in the entries database.

        IMPORTANT RULES:
        - ALWAYS use the similarity search tool to find relevant information before answering any question.
        - ONLY answer based on the results returned by the similarity search tool.
        - If the similarity search returns no relevant results, or the results do not contain enough information to answer the question, clearly tell the user that you could not find the answer in the available entries.
        - Do NOT use general knowledge, make assumptions, or fabricate information that is not present in the search results.
        - Do NOT search the internet or reference external sources.
        - When answering, you may summarize and synthesize information from multiple search results, but never add information that isn't in the results.
        - ALWAYS format your answer in Markdown. Use headings, lists, bold, and other Markdown syntax to make the response clear and readable.
        - Do NOT use emojis in your responses.
        INSTRUCTIONS;
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'answer' => $schema
                ->string()
                ->description('The answer formatted in Markdown. Use headings, lists, bold, and other Markdown syntax for readability.')
                ->required(),
        ];
    }

    public function tools(): iterable
    {
        return [
            SimilaritySearch::usingModel(model: EntryEmbeddingChunk::class, column: 'embedding', limit: 25),
        ];
    }

    public function messages(): iterable
    {
        if (!$this->conversationId) {
            return [];
        }

        return $this->messageService
            ->getMessages($this->conversationId)
            ->map(fn(Message $message) => match ($message->role) {
                MessageRole::User => new UserMessage($message->content),
                MessageRole::AiAssistant => new AssistantMessage($message->content),
            })
            ->all();
    }
}
