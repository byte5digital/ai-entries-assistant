<?php

declare(strict_types=1);

namespace Byte5\AiEntriesAssistant\Models;

use Byte5\AiEntriesAssistant\Database\Factories\ConversationFactory;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/** @use HasFactory<ConversationFactory> */
final class Conversation extends Model
{
    /** @use HasFactory<ConversationFactory> */
    use HasFactory;
    use HasUuids;

    protected static function newFactory(): ConversationFactory
    {
        return ConversationFactory::new();
    }

    protected $table = 'ai_entry_assistant_conversations';

    protected $fillable = [
        'id',
        'user_id',
        'title',
    ];

    /** @return HasMany<Message, $this> */
    public function messages(): HasMany
    {
        return $this->hasMany(Message::class, 'conversation_id');
    }

    /** @return HasOne<Message, $this> */
    public function latestMessage(): HasOne
    {
        return $this->hasOne(Message::class, 'conversation_id')->latestOfMany();
    }

    /** @return Attribute<string, never> */
    protected function shortTitle(): Attribute
    {
        return Attribute::make(
            get: fn (): string => \Illuminate\Support\Str::limit($this->title ?? '', 20),
        );
    }

    /** @param Builder<self> $query */
    #[Scope]
    protected function forUser(Builder $query, string $userId): void
    {
        $query->where('user_id', $userId);
    }
}
