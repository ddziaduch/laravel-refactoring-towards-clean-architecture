<?php

namespace Conduit\Article\Application\Port\Secondary;

interface FavoriteRepository
{
    /**
     * Idempotent: a user favorites an article at most once.
     */
    public function add(int $articleId, int $userId): void;

    public function remove(int $articleId, int $userId): void;
}
