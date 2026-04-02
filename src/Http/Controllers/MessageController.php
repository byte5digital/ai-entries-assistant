<?php

declare(strict_types=1);

namespace Byte5\AiEntriesAssistant\Http\Controllers;

use Byte5\AiEntriesAssistant\Http\Requests\AddMessageToConversationRequest;
use Byte5\AiEntriesAssistant\Http\Resources\MessageResource;
use Byte5\AiEntriesAssistant\Models\Conversation;
use Byte5\AiEntriesAssistant\Models\Message;
use Byte5\AiEntriesAssistant\Services\Contracts\MessageServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Statamic\Http\Controllers\CP\CpController;

final class MessageController extends CpController
{
    public function index(Request $request, Conversation $conversation): AnonymousResourceCollection
    {
        $query = $conversation->messages();

        if ($request->has('since')) {
            $sinceMessage = $conversation->messages()->find((string) $request->get('since'));

            if ($sinceMessage instanceof Message) {
                $query = $query->where('created_at', '>', $sinceMessage->created_at)->oldest();
            }

            return MessageResource::collection($query->get());
        }

        return MessageResource::collection($query->latest()->cursorPaginate(20));
    }

    public function store(
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
