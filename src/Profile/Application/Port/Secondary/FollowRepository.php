<?php

namespace Conduit\Profile\Application\Port\Secondary;

use Conduit\Profile\Domain\Follow;

interface FollowRepository
{
    /**
     * Idempotent: following twice keeps a single follow.
     */
    public function add(Follow $follow): void;

    public function remove(Follow $follow): void;

    /**
     * @param  list<int>  $candidateIds
     * @return list<int> the candidates the follower follows
     */
    public function followedAmong(int $followerId, array $candidateIds): array;

    /**
     * @return list<int>
     */
    public function followedBy(int $followerId): array;
}
