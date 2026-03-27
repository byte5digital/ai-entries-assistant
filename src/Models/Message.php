<?php

declare(strict_types=1);

namespace Byte5\AiEntriesAssistant\Models;

use Byte5\AiEntriesAssistant\Database\Factories\MessageFactory;
use Byte5\AiEntriesAssistant\Enums\MessageRole;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** @use HasFactory<MessageFactory> */
final class Message extends Model
{
    /** @use HasFactory<MessageFactory> */
    use HasFactory;
    use HasUuids;
    use HasTimestamps;

    protected $table = 'ai_entry_assistant_messages';
    protected $fillable = [
        'conversation_id',
        'user_id',
        'role',
        'content',
    ];

    protected static function newFactory(): MessageFactory
    {
        return MessageFactory::new();
    }

    /** @return BelongsTo<Conversation, $this> */
    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class, 'conversation_id');
    }

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'role' => MessageRole::class,
        ];
    }

    /** @param  Builder<self>  $query */
    #[Scope]
    protected function userMessages(Builder $query): void
    {
        $query->where('role', MessageRole::User);
    }

    /** @param  Builder<self>  $query */
    #[Scope]
    protected function assistantMessages(Builder $query): void
    {
        $query->where('role', MessageRole::AiAssistant);
    }
}
