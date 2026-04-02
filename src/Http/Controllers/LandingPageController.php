<?php

declare(strict_types=1);

namespace Byte5\AiEntriesAssistant\Http\Controllers;

use Byte5\AiEntriesAssistant\Http\Resources\ConversationResource;
use Byte5\AiEntriesAssistant\Models\Conversation;
use Byte5\AiEntriesAssistant\Repositories\Contracts\ConversationRepositoryInterface;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Statamic\Http\Controllers\CP\CpController;

final class LandingPageController extends CpController
{
    public function index(Request $request, ConversationRepositoryInterface $conversationRepository): Response
    {
        $userId = $request->user()->getAuthIdentifier();

        $conversations = Conversation::query()
            ->forUser($userId)
            ->latest()
            ->cursorPaginate(20);
        
        return Inertia::render('ai-entries-assistant::LandingPage', [
            'landingPageUrl' => cp_route('ai-entries-assistant.index'),
            'startConversationUrl' => cp_route('ai-entries-assistant.conversations.store'),
            'conversationsIndexUrl' => cp_route('ai-entries-assistant.conversations.index'),
            'initialConversations' => ConversationResource::collection($conversations)->response($request)->getData(true),
        ]);
    }
}
