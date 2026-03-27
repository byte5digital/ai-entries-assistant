<?php

namespace Byte5\AiEntriesAssistant\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddMessageToConversationRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'content' => ['required', 'string'],
        ];
    }
}