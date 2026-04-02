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
use Illuminate\Support\Carbon;

/**
 * @property string $id
 * @property string $conversation_id
 * @property string|null $user_id
 * @property MessageRole $role
 * @property string $content
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @use HasFactory<MessageFactory>
 */
final class Message extends Model
{
    /** @use HasFactory<MessageFactory> */
    use HasFactory;

    use HasTimestamps;
    use HasUuids;

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
