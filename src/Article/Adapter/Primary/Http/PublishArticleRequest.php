<?php

namespace Conduit\Article\Adapter\Primary\Http;

use Illuminate\Foundation\Http\FormRequest;

final class PublishArticleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'article' => 'required|array',
            'article.title' => 'required|string|max:255',
            'article.description' => 'required|string|max:255',
            'article.body' => 'required|string|max:2048',
            'article.tagList' => 'sometimes|array',
            'article.tagList.*' => 'sometimes|string|max:255',
        ];
    }
}
