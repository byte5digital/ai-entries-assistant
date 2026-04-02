# AI Entries Assistant

AI Entries Assistant is a Statamic addon that provides a conversational AI assistant in the control panel. It
uses [Laravel AI](https://laravel.com/docs/master/ai-sdk) and RAG (Retrieval-Augmented Generation) powered
by [AI Entry Embeddings](https://github.com/byte5digital/ai-entry-embeddings) to answer questions hidden in your
Statamic content.

## How It Works

1. A user starts a conversation from the Statamic control panel.
2. The user's message is stored and the `UserMessageAddedToConversation` event is dispatched.
3. A queued listener invokes the `EntriesAssistantAgent`, which performs a **similarity search** against entry
   embeddings to find relevant content chunks.
4. The agent produces a Markdown-formatted answer based exclusively on the search results and stores it as an AI
   assistant message.
5. The response is delivered to the frontend via broadcasting (Laravel Echo) or polling fallback.

## Requirements

- PHP 8.3+
- Statamic 6.0+
- PostgreSQL with the [pgvector](https://github.com/pgvector/pgvector) extension
- [byte5/ai-entry-embeddings](https://github.com/byte5digital/ai-entry-embeddings) addon (installed automatically as a
  dependency)
- A configured AI provider via [Laravel AI](https://github.com/laravel/ai) (e.g. Anthropic, OpenAI)

## Installation

```bash
composer require byte5/ai-entries-assistant
```

Publish the configuration file:

```bash
php artisan vendor:publish --tag=ai-entries-assistant-config
```

Run migrations (creates the `ai_entry_assistant_conversations` and `ai_entry_assistant_messages` tables):

```bash
php artisan migrate
```

## Configuration

The configuration file is published to `config/ai-entries-assistant.php`.

### AI Provider

By default the addon uses whatever provider is set in `config('ai.default')`. To use a different provider for the
assistant, set it explicitly:

```php
'provider' => 'anthropic',
```

Supported providers include Anthropic, OpenAI, Gemini, Ollama, Azure, DeepSeek, Groq, Mistral, OpenRouter, and XAI.

### Queue

AI response generation runs on a queue. Configure which queue to use:

```php
'jobs_queue' => 'default',
```

### Broadcasting

Real-time message delivery uses Laravel Broadcasting. Disabled by default -- enable it via your `.env`:

```dotenv
AI_ENTRIES_ASSISTANT_BROADCASTING=true
```

When disabled, the frontend falls back to polling. You can also configure a separate queue for broadcast events:

```php
'broadcasting_queue' => 'default',
```

## Events

| Event                                   | When                          | Broadcast                                      |
|-----------------------------------------|-------------------------------|------------------------------------------------|
| `ConversationStarted`                   | A new conversation is created | --                                             |
| `ConversationDeleted`                   | A conversation is deleted     | --                                             |
| `UserMessageAddedToConversation`        | User sends a message          | `user.message.created` on private channel      |
| `AiAssistantMessageAddedToConversation` | AI assistant responds         | `assistant.message.created` on private channel |

Broadcast events are sent to the `conversation.{conversationId}` private channel. Each user can subscribe only to their
own private channel.

## Control Panel

The addon registers a navigation item under **AI Tools** in the Statamic control panel. Access is gated by the
`access AI assistant` permission.

## License

This addon is open-sourced software licensed under the GNU General Public License v3.0 (GPL-3.0). See [LICENSE](LICENSE)
for details.
