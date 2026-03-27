<?php

declare(strict_types=1);

namespace Byte5\AiEntriesAssistant\Http\Controllers;

use Byte5\AiEntriesAssistant\Models\Conversation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Statamic\Http\Controllers\CP\CpController;

final class ConversationController extends CpController
{
    public function index(Request $request): JsonResponse
    {
        $userId = (string) $request->user()?->getAuthIdentifier();

        $conversations = Conversation::query()
            ->forUser($userId)
            ->latest('updated_at')
            ->get(['id', 'title', 'created_at', 'updated_at']);

        return response()->json(['data' => $conversations]);
    }

    public function show(Request $request, string $conversation): JsonResponse
    {
        $userId = (string) $request->user()?->getAuthIdentifier();

        $conversation = Conversation::query()
            ->forUser($userId)
            ->with(['messages' => fn ($query) => $query->orderBy('created_at')])
            ->findOrFail($conversation);

        return response()->json(['data' => $conversation]);
    }

    public function destroy(Request $request, string $conversation): JsonResponse
    {
        $userId = (string) $request->user()?->getAuthIdentifier();

        $conversation = Conversation::query()
            ->forUser($userId)
            ->findOrFail($conversation);

        $conversation->delete();

        return response()->json(['message' => 'Conversation deleted.']);
    }
}
