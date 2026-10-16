<?php

namespace Conduit\Article\Adapter\Secondary\Database;

use Conduit\Article\Application\Port\Secondary\FavoriteRepository;
use Illuminate\Database\ConnectionInterface;

final readonly class DatabaseFavoriteRepository implements FavoriteRepository
{
    public function __construct(private ConnectionInterface $connection) {}

    public function add(int $articleId, int $userId): void
    {
        $this->connection->table('article_user')->insertOrIgnore([
            'article_id' => $articleId,
            'user_id' => $userId,
        ]);
    }

    public function remove(int $articleId, int $userId): void
    {
        $this->connection->table('article_user')
            ->where('article_id', $articleId)
            ->where('user_id', $userId)
            ->delete();
    }
}
