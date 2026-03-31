<?php

declare(strict_types=1);

namespace Byte5\AiEntriesAssistant\Http\Controllers;

use Byte5\AiEntriesAssistant\Http\Requests\AddMessageToConversationRequest;
use Byte5\AiEntriesAssistant\Http\Resources\MessageResource;
use Byte5\AiEntriesAssistant\Models\Conversation;
use Byte5\AiEntriesAssistant\Services\Contracts\ConversationServiceInterface;
use Byte5\AiEntriesAssistant\Services\Contracts\MessageServiceInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Inertia\Inertia;
use Statamic\Http\Controllers\CP\CpController;

final class ConversationController extends CpController
{
    public function index(Request $request)
    {
        return Inertia::render('ai-entries-assistant::Conversations');
    }

    public function store(
        AddMessageToConversationRequest $request,
        ConversationServiceInterface $conversationService,
    ): RedirectResponse {
        $userId = $request->user()->getAuthIdentifier();

        $conversation = $conversationService->startConversation(
            $request->validated('content'),
            (string) $userId,
        );

        return redirect()->cpRoute('ai-entries-assistant.conversations.show', $conversation->id);
    }

    public function show(Request $request, Conversation $conversation)
    {
        $messages = $conversation->messages()
            ->latest()
            ->cursorPaginate(20);

        return Inertia::render('ai-entries-assistant::Conversation', [
            'conversationId' => $conversation->id,
            'conversationTitle' => $conversation->title,
            'initialMessages' => MessageResource::collection($messages)->response($request)->getData(true),
            'messagesUrl' => cp_route('ai-entries-assistant.conversations.messages', $conversation->id),
            'storeMessageUrl' => cp_route('ai-entries-assistant.conversations.messages.store', $conversation->id),
            'messageFetchingStrategy' => config('ai-entries-assistant.message_fetching', 'polling'),
        ]);
    }

    public function messages(Request $request, Conversation $conversation): AnonymousResourceCollection
    {
        $query = $conversation->messages();

        if ($request->has('since')) {
            $sinceMessage = $conversation->messages()->find($request->get('since'));

            if ($sinceMessage) {
                $query = $query->where('created_at', '>', $sinceMessage->created_at)
                    ->orderBy('created_at');
            }

            return MessageResource::collection($query->get());
        }

        return MessageResource::collection($query->latest()->cursorPaginate(20));
    }

    public function storeMessage(
        AddMessageToConversationRequest $request,
        Conversation $conversation,
        MessageServiceInterface $messageService,
    ): MessageResource {
        $userId = (string) $request->user()->getAuthIdentifier();

        $message = $messageService->createUserMessage(
            $conversation->id,
            $userId,
            $request->validated('content'),
        );

        return new MessageResource($message);
    }
}
