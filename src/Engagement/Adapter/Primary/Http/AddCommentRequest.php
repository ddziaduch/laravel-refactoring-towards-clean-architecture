<?php

namespace Conduit\Engagement\Adapter\Primary\Http;

use Illuminate\Foundation\Http\FormRequest;

final class AddCommentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'comment' => 'required|array',
            'comment.body' => 'required|string|max:2048',
        ];
    }

    public function body(): string
    {
        return $this->validated('comment.body');
    }
}
