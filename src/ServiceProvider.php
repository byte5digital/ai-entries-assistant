<?php

declare(strict_types=1);

namespace Byte5\AiEntriesAssistant;

use Byte5\AiEntriesAssistant\Repositories\Contracts\ConversationRepositoryInterface;
use Byte5\AiEntriesAssistant\Repositories\ConversationRepository;
use Byte5\AiEntriesAssistant\Services\Contracts\ConversationServiceInterface;
use Byte5\AiEntriesAssistant\Services\Contracts\MessageServiceInterface;
use Byte5\AiEntriesAssistant\Services\ConversationService;
use Byte5\AiEntriesAssistant\Services\MessageService;
use Statamic\CP\Navigation\Nav;
use Statamic\Facades\CP\Nav as NavAPI;
use Statamic\Facades\Permission;
use Statamic\Providers\AddonServiceProvider;

final class ServiceProvider extends AddonServiceProvider
{
    protected $vite = [
        'input' => [
            'resources/js/addon.js',
            'resources/css/addon.css',
        ],
        'publicDirectory' => 'resources/dist',
    ];

    public function bootAddon(): void
    {
        $this->publishes([
            __DIR__.'/../config/ai-entries-assistant.php' => config_path('ai-entries-assistant.php'),
        ], 'config');
        $this->loadTranslationsFrom(__DIR__.'/../lang', 'ai-entries-assistant');
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        $this->bootNav();
        $this->bootPermissions();
    }

    private function bootNav(): void
    {
        NavAPI::extend(function (Nav $nav): void {
            $nav->content(__('ai-entries-assistant::frontend.navigation.title'))
                ->section('AI Tools')
                ->can('access AI assistant')
                ->route('ai-entries-assistant.index')
                ->icon('ai-spark');
        });
    }

    private function bootPermissions(): void
    {
        Permission::register('access AI assistant')
            ->label(__('ai-entries-assistant::permissions.access.title'));
    }

    public function register(): void
    {
        parent::register();

        $this->mergeConfigFrom(__DIR__.'/../config/ai-entries-assistant.php', 'ai-entries-assistant');
        $this->app->bind(ConversationRepositoryInterface::class, ConversationRepository::class);
        $this->app->bind(ConversationServiceInterface::class, ConversationService::class);
        $this->app->bind(MessageServiceInterface::class, MessageService::class);
    }
}
