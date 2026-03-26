<?php

declare(strict_types=1);

namespace Byte5\AiEntriesChatbot;

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
            __DIR__.'/../config/ai-entries-chatbot.php' => config_path('ai-entries-chatbot.php'),
        ], 'config');
        $this->loadTranslationsFrom(__DIR__.'/../lang', 'ai-entries-chatbot');
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        $this->bootNav();
        $this->bootPermissions();
    }

    private function bootNav(): void
    {
        NavAPI::extend(function (Nav $nav): void {
            $nav->content(__('ai-entries-chatbot::frontend.navigation.title'))
                ->section('AI Tools')
                ->can('access AI chatbot')
                ->route('ai-entries-chatbot.index')
                ->icon('ai-spark');
        });
    }

    private function bootPermissions(): void
    {
        Permission::register('access AI chatbot')
            ->label(__('ai-entries-chatbot::permissions.access.title'));
    }

    public function register(): void
    {
        parent::register();

        $this->mergeConfigFrom(__DIR__.'/../config/ai-entries-chatbot.php', 'ai-entries-chatbot');
    }
}