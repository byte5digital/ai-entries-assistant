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


];