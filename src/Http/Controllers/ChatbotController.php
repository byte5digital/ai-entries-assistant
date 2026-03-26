<?php

declare(strict_types=1);

namespace Byte5\AiEntriesChatbot\Http\Controllers;

use Inertia\Inertia;
use Inertia\Response;
use Statamic\Http\Controllers\CP\CpController;

final class ChatbotController extends CpController
{
    public function index(): Response
    {
        return Inertia::render('ai-entries-chatbot::Chatbot');
    }
}