<?php

declare(strict_types=1);

namespace Byte5\AiEntriesAssistant\Http\Resources;

use Byte5\AiEntriesAssistant\Models\Conversation;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Conversation
 */
class ConversationResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'short_title' => $this->short_title,
            'url' => cp_route('ai-entries-assistant.conversations.show', $this->id),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
