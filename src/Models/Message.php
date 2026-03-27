<?php

declare(strict_types=1);

namespace Byte5\AiEntriesAssistant\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class Message extends Model
{
    protected $table = 'agent_conversation_messages';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'id',
        'conversation_id',
        'user_id',
        'agent',
        'role',
        'content',
        'attachments',
        'tool_calls',
        'tool_results',
        'usage',
        'meta',
    ];

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'attachments' => 'array',
            'tool_calls' => 'array',
            'tool_results' => 'array',
            'usage' => 'array',
            'meta' => 'array',
        ];
    }

    /** @return BelongsTo<Conversation, $this> */
    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class, 'conversation_id');
    }

    /** @param Builder<self> $query */
    public function scopeUserMessages(Builder $query): void
    {
        $query->where('role', 'user');
    }

    /** @param Builder<self> $query */
    public function scopeAssistantMessages(Builder $query): void
    {
        $query->where('role', 'assistant');
    }
}
