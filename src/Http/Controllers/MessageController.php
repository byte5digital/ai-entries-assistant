<?php

declare(strict_types=1);

namespace Byte5\AiEntriesAssistant\Http\Controllers;

use Byte5\AiEntriesAssistant\Agents\AssistantAgent;
use Byte5\AiEntriesAssistant\Support\ConversationUser;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Statamic\Http\Controllers\CP\CpController;

final class MessageController extends CpController
{
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'content' => ['required', 'string', 'max:10000'],
            'conversation_id' => ['nullable', 'string', 'max:36'],
        ]);

        $conversationUser = new ConversationUser((string) $request->user()?->getAuthIdentifier());
        $agent = AssistantAgent::make();

        if ($request->filled('conversation_id')) {
            $agent->continue($request->input('conversation_id'), $conversationUser);
        } else {
            $agent->forUser($conversationUser);
        }

        $response = $agent->prompt($request->input('content'));

        return response()->json([
            'conversation_id' => $response->conversationId,
            'message' => [
                'role' => 'assistant',
                'content' => $response->text,
            ],
        ]);
    }
}
