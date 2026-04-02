<?php

namespace Byte5\AiEntriesAssistant\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AddMessageToConversationRequest extends FormRequest
{
    /** @return array<string, array<int, string|Rule>> */
    public function rules(): array
    {
        return [
            'content' => ['required', 'string'],
        ];
    }
}
