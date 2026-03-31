<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Queue Name
    |--------------------------------------------------------------------------
    |
    | The queue that AI assistant jobs (e.g. generating replies) are dispatched
    | to. Set this to a dedicated queue name if you want to isolate AI
    | processing from other queued work in your application.
    |
    */

    'queue' => env('AI_ENTRIES_ASSISTANT_QUEUE', 'default'),

    /*
    |--------------------------------------------------------------------------
    | Message Fetching Strategy
    |--------------------------------------------------------------------------
    |
    | How the frontend receives new AI assistant messages after they are
    | generated asynchronously by the queue worker.
    |
    | Supported: "polling", "echo"
    |
    | "polling" - The frontend polls the server every few seconds for new
    |             messages. Works out of the box with no extra infrastructure.
    |
    | "echo"    - Uses Laravel Echo with a broadcast driver (Pusher, Reverb,
    |             Soketi, etc.) for real-time delivery. Requires additional
    |             setup — see the Laravel Broadcasting documentation.
    |
    */

    'message_fetching' => env('AI_ENTRIES_ASSISTANT_MESSAGE_FETCHING', 'polling'),

];