<?php

declare(strict_types=1);

namespace Byte5\AiEntriesChatbot;

use Statamic\Providers\AddonServiceProvider;

final class ServiceProvider extends AddonServiceProvider
{
    public function bootAddon(): void
    {
        $this->publishes([
            __DIR__.'/../config/ai-entries-chatbot.php' => config_path('ai-entries-chatbot.php'),
        ], 'config');
        $this->loadTranslationsFrom(__DIR__.'/../lang', 'ai-entries-chatbot');
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }

    public function register(): void
    {
        parent::register();

        $this->mergeConfigFrom(__DIR__.'/../config/ai-entries-chatbot.php', 'ai-entries-chatbot');
    }
}