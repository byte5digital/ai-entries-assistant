<?php

declare(strict_types=1);

return [
    'agent' => [
        'provider' => env('AI_ENTRIES_ASSISTANT_PROVIDER'),
        'model' => env('AI_ENTRIES_ASSISTANT_MODEL'),
        'instructions' => 'You are a helpful assistant that answers questions about the content of this website. Be concise and accurate.',
        'max_conversation_messages' => 100,
    ],
];