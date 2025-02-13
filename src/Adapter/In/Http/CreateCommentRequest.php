<?php

namespace Clean\Adapter\In\Http;

use Illuminate\Foundation\Http\FormRequest;

/**
 * @property array{ body: string } $comment
 */
class CreateCommentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'comment.body' => 'required|string|max:2048'
        ];
    }
}
