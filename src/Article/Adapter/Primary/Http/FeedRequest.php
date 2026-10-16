<?php

namespace Conduit\Article\Adapter\Primary\Http;

use Illuminate\Foundation\Http\FormRequest;

final class FeedRequest extends FormRequest
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

    public function limit(): int
    {
        return (int) $this->validated('limit', 20);
    }

    public function offset(): int
    {
        return (int) $this->validated('offset', 0);
    }
}
