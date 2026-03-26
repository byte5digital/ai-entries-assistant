<?php

declare(strict_types=1);

use Byte5\AiEntriesChatbot\Http\Controllers\ChatbotController;

Route::name('ai-entries-chatbot.')->prefix('ai-entries-chatbot')->group(function () {
    Route::get('/', [ChatbotController::class, 'index'])->name('index');
});
