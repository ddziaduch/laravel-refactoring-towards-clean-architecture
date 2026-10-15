<?php

declare(strict_types=1);

namespace Clean\Adapter\Outgoing;

use App\Models\Article;
use Clean\Application\Port\Outgoing\ArticleRepository;

final class LaravelArticleRepository implements ArticleRepository
{
    public function existsBySlug(string $articleSlug): bool
    {
        $article = Article::query()
            ->where(['slug' => $articleSlug])
            ->first();

        return $article !== null;
    }
}
