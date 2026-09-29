<?php

namespace App\Http\Resources;

class ArticleListResource extends ArticleResource
{
    public function toArray($request): array
    {
        $article = parent::toArray($request);
        unset($article['body']);

        return $article;
    }
}
