<?php

declare(strict_types=1);

use Byte5\AiEntriesAssistant\Http\Controllers\ConversationController;
use Byte5\AiEntriesAssistant\Http\Controllers\LandingPageController;

Route::name('ai-entries-assistant.')->prefix('ai-entries-assistant')->group(function () {
    Route::get('/', [LandingPageController::class, 'index'])->name('index');
    Route::name('conversations.')->prefix('conversations')->group(function () {
        Route::get('/', [ConversationController::class, 'index'])->name('index');
        Route::post('/', [ConversationController::class, 'store'])->name('store');
        Route::get('/{conversation}', [ConversationController::class, 'show'])->name('show');
        Route::patch('/{conversation}/title', [ConversationController::class, 'updateTitle'])->name('title.update');
        Route::delete('/{conversation}', [ConversationController::class, 'destroy'])->name('destroy');
        Route::get('/{conversation}/messages', [ConversationController::class, 'messages'])->name('messages');
        Route::post('/{conversation}/messages', [ConversationController::class, 'storeMessage'])->name('messages.store');
    });
});
