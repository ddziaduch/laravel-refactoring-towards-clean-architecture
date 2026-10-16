<?php

namespace Conduit\Article\Adapter\Primary\Http;

use Conduit\Article\Application\Port\Primary\ArticleFilter;
use Illuminate\Foundation\Http\FormRequest;

final class ListArticlesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'tag' => 'sometimes|string',
            'author' => 'sometimes|string',
            'favorited' => 'sometimes|string',
            'limit' => 'sometimes|integer|min:1',
            'offset' => 'sometimes|integer|min:0',
        ];
    }

    public function filter(): ArticleFilter
    {
        return new ArticleFilter(
            $this->validated('tag'),
            $this->validated('author'),
            $this->validated('favorited'),
            (int) $this->validated('limit', 20),
            (int) $this->validated('offset', 0),
        );
    }
}
