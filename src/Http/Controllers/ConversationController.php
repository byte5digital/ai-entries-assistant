<?php

declare(strict_types=1);

namespace Byte5\AiEntriesAssistant\Http\Controllers;

use Byte5\AiEntriesAssistant\Http\Requests\AddMessageToConversationRequest;
use Byte5\AiEntriesAssistant\Http\Requests\UpdateConversationTitleRequest;
use Byte5\AiEntriesAssistant\Http\Resources\ConversationResource;
use Byte5\AiEntriesAssistant\Http\Resources\MessageResource;
use Byte5\AiEntriesAssistant\Models\Conversation;
use Byte5\AiEntriesAssistant\Services\Contracts\ConversationServiceInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Inertia\Inertia;
use Inertia\Response;
use Statamic\Http\Controllers\CP\CpController;

final class ConversationController extends CpController
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $userId = (string) $request->user()->getAuthIdentifier();

        $conversations = Conversation::query()
            ->forUser($userId)
            ->latest()
            ->cursorPaginate(20);

        return ConversationResource::collection($conversations);
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

    public function show(Request $request, Conversation $conversation): Response
    {
        $userId = (string) $request->user()->getAuthIdentifier();

        $messages = $conversation->messages()
            ->latest()
            ->cursorPaginate(20);

        $conversations = Conversation::query()
            ->forUser($userId)
            ->latest()
            ->cursorPaginate(20);

        return Inertia::render('ai-entries-assistant::Conversation', [
            'conversationId' => $conversation->id,
            'conversationTitle' => $conversation->title,
            'initialMessages' => MessageResource::collection($messages)->response($request)->getData(true),
            'initialConversations' => ConversationResource::collection($conversations)->response($request)->getData(true),
            'landingPageUrl' => cp_route('ai-entries-assistant.index'),
            'conversationsIndexUrl' => cp_route('ai-entries-assistant.conversations.index'),
            'conversationMessagesIndexUrl' => cp_route('ai-entries-assistant.conversations.messages.index',
                $conversation->id),
            'conversationMessagesStoreUrl' => cp_route('ai-entries-assistant.conversations.messages.store',
                $conversation->id),
            'conversationTitleUpdateUrl' => cp_route('ai-entries-assistant.conversations.title.update',
                $conversation->id),
            'conversationDestroyUrl' => cp_route('ai-entries-assistant.conversations.destroy', $conversation->id),
        ]);
    }

    public function updateTitle(
        UpdateConversationTitleRequest $request,
        Conversation $conversation,
        ConversationServiceInterface $conversationService,
    ): ConversationResource {
        $conversationService->updateTitle(
            $conversation,
            $request->validated('title'),
        );

        return new ConversationResource($conversation);
    }

    public function destroy(
        Conversation $conversation,
        ConversationServiceInterface $conversationService,
    ): RedirectResponse {
        $conversationService->deleteConversation($conversation);

        return redirect()->cpRoute('ai-entries-assistant.index');
    }
}
