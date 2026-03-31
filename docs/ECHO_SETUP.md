# Echo Setup Guide

This guide explains how to configure your host application to use real-time message delivery (Echo strategy) for the AI Entries Assistant addon.

## Prerequisites

The addon supports any Laravel-compatible broadcast driver. Choose one:

| Driver | Description |
|--------|-------------|
| [Laravel Reverb](https://laravel.com/docs/reverb) | First-party, self-hosted WebSocket server |
| [Pusher](https://pusher.com) | Managed WebSocket service |
| [Soketi](https://soketi.app) | Open-source, self-hosted Pusher alternative |
| [Ably](https://ably.com) | Managed real-time messaging platform |

## 1. Install Backend Dependencies

Install the broadcast driver package for your chosen provider. For example, with Reverb:

```bash
php artisan install:broadcasting
```

Or with Pusher:

```bash
composer require pusher/pusher-php-server
```

## 2. Install Frontend Dependencies

```bash
npm install laravel-echo
```

If using Pusher or Soketi, also install the Pusher JS client:

```bash
npm install pusher-js
```

## 3. Configure Environment

Add the following to your `.env` file:

```env
BROADCAST_CONNECTION=reverb   # or: pusher, ably

# For the AI Entries Assistant addon:
AI_ENTRIES_ASSISTANT_MESSAGE_FETCHING=echo
```

Add the driver-specific credentials as documented by your chosen provider (e.g. `REVERB_APP_ID`, `REVERB_APP_KEY`, etc.).

## 4. Bootstrap Echo

In your application's `resources/js/app.js` (or equivalent bootstrap file), configure Echo:

```js
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'reverb',  // or: 'pusher'
    key: import.meta.env.VITE_REVERB_APP_KEY,
    wsHost: import.meta.env.VITE_REVERB_HOST,
    wsPort: import.meta.env.VITE_REVERB_PORT ?? 80,
    wssPort: import.meta.env.VITE_REVERB_PORT ?? 443,
    forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? 'https') === 'https',
    enabledTransports: ['ws', 'wss'],
});
```

Adjust the configuration based on your chosen driver. See the [Laravel Broadcasting documentation](https://laravel.com/docs/broadcasting) for details.

## 5. Run Queue Worker

The AI reply generation and event broadcasting both run on queues. Make sure your queue worker is running:

```bash
php artisan queue:work --queue=default
```

If you configured custom queue names in `config/ai-entries-assistant.php`, include those:

```bash
php artisan queue:work --queue=your-custom-queue
```

## 6. Verify

1. Open the AI Entries Assistant in the Statamic Control Panel
2. Send a message
3. The thinking indicator should appear
4. Once the queue worker processes the job, the AI response should appear instantly without a page refresh

## How It Works

```
User sends message
  -> POST to server (user message saved)
  -> UserMessageAddedToConversation event dispatched
  -> GenerateAiAssistantReply queued listener picks it up
  -> AI agent generates response
  -> AI message saved to database
  -> AiAssistantMessageAddedToConversation event dispatched
  -> Event broadcasts on private channel: conversation.{id}
  -> Laravel Echo receives the broadcast
  -> Message appears in the chat UI
```

## Channel Authorization

The addon registers a private channel `conversation.{conversationId}`. Only the user who owns the conversation can subscribe to it. This is handled automatically by the addon's service provider.

## Troubleshooting

- **Messages don't appear in real-time**: Check that `window.Echo` is defined in the browser console. If not, Echo is not bootstrapped correctly.
- **403 on channel subscription**: The authenticated user doesn't match the conversation's `user_id`. Check that the Statamic CP session is active.
- **Falling back to polling**: If `window.Echo` is undefined, the addon logs a warning and falls back to no-op. The polling strategy continues to work as a fallback if configured.
