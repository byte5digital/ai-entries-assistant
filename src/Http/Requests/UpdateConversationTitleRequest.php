<?php

declare(strict_types=1);

namespace Byte5\AiEntriesAssistant\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateConversationTitleRequest extends FormRequest
{
    /** @return array<string, array<int, string>> */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
        ];
    }
}
