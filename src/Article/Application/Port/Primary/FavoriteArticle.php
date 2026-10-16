<?php

namespace Conduit\Article\Application\Port\Primary;

use Conduit\Article\Application\Exception\ArticleNotFound;

interface FavoriteArticle
{
    /**
     * Idempotent: favoriting twice counts once.
     *
     * @throws ArticleNotFound
     */
    public function __invoke(string $slug, int $userId): void;
}
