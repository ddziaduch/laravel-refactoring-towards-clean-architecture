<?php

namespace Clean\Adapter\Secondary\Eloquent;

use App\Models\Article;
use Clean\Application\Port\Secondary\ArticleRepository;

final class EloquentArticleRepository implements ArticleRepository
{
    public function findIdBySlug(string $slug): ?int
    {
        return Article::query()->where('slug', $slug)->value('id');
    }
}
