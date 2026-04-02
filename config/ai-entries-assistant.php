<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Jobs Queue
    |--------------------------------------------------------------------------
    |
    | The queue used for AI assistant jobs such as generating replies.
    | Set this to a dedicated queue name if you want to isolate AI
    | processing from other queued work in your application.
    |
    */

    'jobs_queue' => env('AI_ENTRIES_ASSISTANT_JOBS_QUEUE', 'default'),

    /*
    |--------------------------------------------------------------------------
    | Broadcasting Queue
    |--------------------------------------------------------------------------
    |
    | The queue used for broadcasting message events to the frontend
    | via Echo. Only relevant when 'message_fetching' is set to 'echo'.
    |
    */

    'broadcasting_queue' => env('AI_ENTRIES_ASSISTANT_BROADCASTING_QUEUE', 'default'),

    /*
    |--------------------------------------------------------------------------
    | Broadcasting
    |--------------------------------------------------------------------------
    |
    | Enable real-time broadcasting of conversation events via Laravel Echo.
    | When enabled, the package will register broadcast channels and push
    | message events to the frontend. Requires a configured broadcast
    | driver (e.g. Reverb, Pusher) in your application.
    |
    */

    'broadcasting' => env('AI_ENTRIES_ASSISTANT_BROADCASTING', false),

    /*
    |--------------------------------------------------------------------------
    | AI Provider
    |--------------------------------------------------------------------------
    |
    | The AI provider to use for generating assistant replies.
    | Supported: "anthropic", "openai", "gemini", "ollama", "azure",
    |            "deepseek", "groq", "mistral", "openrouter", "xai"
    |
    | When set to null, the default provider from config('ai.default')
    | will be used.
    |
    */

    'provider' => env('AI_ENTRIES_ASSISTANT_PROVIDER'),

];
