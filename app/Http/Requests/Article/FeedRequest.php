<?php

namespace App\Http\Requests\Article;

use Illuminate\Foundation\Http\FormRequest;

class FeedRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'limit' => 'sometimes|integer|min:1',
            'offset' => 'sometimes|integer|min:0',
        ];
    }
}
