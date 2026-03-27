<?php

declare(strict_types=1);

namespace Byte5\AiEntriesAssistant\Http\Controllers;

use Inertia\Inertia;
use Inertia\Response;
use Statamic\Http\Controllers\CP\CpController;

final class AssistantController extends CpController
{
    public function index(): Response
    {
        return Inertia::render('ai-entries-assistant::Assistant', [
            'apiUrls' => [
                'conversations' => cp_route('ai-entries-assistant.conversations.index'),
                'chat' => cp_route('ai-entries-assistant.chat'),
            ],
        ]);
    }
}
