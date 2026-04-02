<?php

declare(strict_types=1);

namespace Byte5\AiEntriesAssistant\Http\Resources;

use Byte5\AiEntriesAssistant\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Message
 */
class MessageResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'conversation_id' => $this->conversation_id,
            'role' => $this->role,
            'content' => $this->content,
            'created_at' => $this->created_at,
        ];
    }
}
