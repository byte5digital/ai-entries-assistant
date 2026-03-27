<?php

declare(strict_types=1);

namespace Byte5\AiEntriesAssistant\Http\Controllers;

use Byte5\AiEntriesAssistant\Repositories\Contracts\ConversationRepositoryInterface;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Statamic\Http\Controllers\CP\CpController;

final class LandingPageController extends CpController
{
    public function index(Request $request, ConversationRepositoryInterface $conversationRepository): Response
    {
        $userIdentifier = $request->user()?->getAuthIdentifier();
        if($userIdentifier){
            $lastConversation = $conversationRepository->getLastConversation($userIdentifier);
        }
        return Inertia::render('ai-entries-assistant::LandingPage',[
            'lastConversationUrl' => isset($lastConversation)
                ? cp_route('ai-entries-assistant.conversations.show', $lastConversation->id)
                : null,
            'startConversationUrl' => cp_route('ai-entries-assistant.conversations.store'),
        ]);
    }
}
