<?php

namespace Conduit\Profile\Adapter\Secondary\Database;

use Conduit\Profile\Application\Port\Secondary\FollowRepository;
use Conduit\Profile\Domain\Follow;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Database\Query\Builder;

final readonly class DatabaseFollowRepository implements FollowRepository
{
    public function __construct(private ConnectionInterface $connection) {}

    public function add(Follow $follow): void
    {
        $this->followers()->insertOrIgnore([
            'follower_id' => $follow->followerId,
            'following_id' => $follow->followedId,
        ]);
    }

    public function remove(Follow $follow): void
    {
        $this->followers()
            ->where('follower_id', $follow->followerId)
            ->where('following_id', $follow->followedId)
            ->delete();
    }

    public function followedAmong(int $followerId, array $candidateIds): array
    {
        if ($candidateIds === []) {
            return [];
        }

        return $this->followers()
            ->where('follower_id', $followerId)
            ->whereIn('following_id', $candidateIds)
            ->pluck('following_id')
            ->map(fn (mixed $id): int => (int) $id)
            ->all();
    }

    public function followedBy(int $followerId): array
    {
        return $this->followers()
            ->where('follower_id', $followerId)
            ->pluck('following_id')
            ->map(fn (mixed $id): int => (int) $id)
            ->all();
    }

    private function followers(): Builder
    {
        return $this->connection->table('followers');
    }
}
