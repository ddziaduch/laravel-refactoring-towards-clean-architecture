<?php

namespace Conduit\Article\Adapter\Primary\Http;

use Conduit\Article\Application\Port\Primary\ArticleChanges;
use Illuminate\Foundation\Http\FormRequest;

final class UpdateArticleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'article' => 'required|array|min:1',
            'article.title' => 'sometimes|string|max:255',
            'article.description' => 'sometimes|string|max:255',
            'article.body' => 'sometimes|string|max:2048',
            'article.tagList' => 'sometimes|array',
            'article.tagList.*' => 'sometimes|string|max:255',
        ];
    }

    public function changes(): ArticleChanges
    {
        return new ArticleChanges(
            $this->validated('article.title'),
            $this->validated('article.description'),
            $this->validated('article.body'),
            $this->validated('article.tagList'),
        );
    }
}
