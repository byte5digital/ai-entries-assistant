<?php

declare(strict_types=1);

use Byte5\AiEntriesAssistant\Http\Controllers\ConversationController;
use Byte5\AiEntriesAssistant\Http\Controllers\LandingPageController;
use Byte5\AiEntriesAssistant\Http\Controllers\MessageController;

Route::name('ai-entries-assistant.')->prefix('ai-entries-assistant')->group(function () {
    Route::get('/', [LandingPageController::class, 'index'])->name('index');
    Route::name('conversations.')->prefix('conversations')->group(function () {
        Route::get('/', [ConversationController::class, 'index'])->name('index');
        Route::post('/', [ConversationController::class, 'store'])->name('store');
        Route::prefix('{conversation}')->group(function () {
            Route::get('/', [ConversationController::class, 'show'])->name('show');
            Route::patch('/title', [ConversationController::class, 'updateTitle'])->name('title.update');
            Route::delete('/', [ConversationController::class, 'destroy'])->name('destroy');
            Route::name('messages.')->prefix('messages')->group(function () {
                Route::get('/', [MessageController::class, 'index'])->name('index');
                Route::post('/', [MessageController::class, 'store'])->name('store');
            });
        });
    });
});
