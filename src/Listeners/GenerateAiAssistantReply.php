<?php

declare(strict_types=1);

namespace Byte5\AiEntriesAssistant\Listeners;

use Byte5\AiEntriesAssistant\Agents\RAG\EntriesAssistantAgent;
use Byte5\AiEntriesAssistant\Events\UserMessageAddedToConversation;
use Byte5\AiEntriesAssistant\Services\Contracts\MessageServiceInterface;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Laravel\Ai\Responses\StructuredAgentResponse;
use Throwable;

final class GenerateAiAssistantReply implements ShouldQueue
{
    use InteractsWithQueue;

    public int $tries = 3;

    public int $timeout = 180;

    /** @var array<int, int> */
    public array $backoff = [10, 30, 60];

    public function __construct(
        private readonly EntriesAssistantAgent $agent,
        private readonly MessageServiceInterface $messageService,
    ) {}

    public function handle(UserMessageAddedToConversation $event): void
    {
        $response = $this->agent
            ->forConversation($event->conversation->id)
            ->prompt($event->message->content);

        $content = $response instanceof StructuredAgentResponse
            ? $response['answer'] : (string) $response;

        $this->messageService->createAiAssistantMessage(
            $event->conversation->id,
            $content,
        );
    }

    public function viaQueue(): string
    {
        return config('ai-entries-assistant.jobs_queue', 'default');
    }

    public function failed(UserMessageAddedToConversation $event, Throwable $exception): void
    {
        Log::error('Failed to generate AI assistant reply', [
            'conversation_id' => $event->conversation->id,
            'message_id' => $event->message->id,
            'error' => $exception->getMessage(),
        ]);

        $this->messageService->createAiAssistantMessage(
            $event->conversation->id,
            'Sorry, I was unable to process your request. Please try again.',
        );
    }
}
