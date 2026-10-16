<?php

namespace Conduit\Article\Application\Port\Secondary;

use Conduit\Article\Domain\Article;

interface ArticleRepository
{
    public function findBySlug(string $slug): ?Article;

    public function add(Article $article): int;

    public function save(Article $article): void;

    public function remove(Article $article): void;

    public function slugExists(string $slug, ?int $exceptArticleId = null): bool;
}
