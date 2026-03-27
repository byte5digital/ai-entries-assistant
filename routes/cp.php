<?php

declare(strict_types=1);

use Byte5\AiEntriesAssistant\Http\Controllers\AssistantController;
use Byte5\AiEntriesAssistant\Http\Controllers\ConversationController;
use Byte5\AiEntriesAssistant\Http\Controllers\MessageController;

Route::name('ai-entries-assistant.')->prefix('ai-entries-assistant')->group(function () {
    Route::get('/', [AssistantController::class, 'index'])->name('index');

    Route::prefix('api')->group(function () {
        Route::get('/conversations', [ConversationController::class, 'index'])->name('conversations.index');
        Route::get('/conversations/{conversation}', [ConversationController::class, 'show'])->name('conversations.show');
        Route::delete('/conversations/{conversation}', [ConversationController::class, 'destroy'])->name('conversations.destroy');
        Route::post('/chat', [MessageController::class, 'store'])->name('chat');
    });
});
