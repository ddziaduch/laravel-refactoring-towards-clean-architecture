<?php

namespace Conduit\Article\Application\Port\Secondary;

use Conduit\Article\Application\ReadModel\AuthorReadModel;

/**
 * Authors as the Article module sees them. Who provides them is an adapter detail.
 */
interface AuthorProfiles
{
    /**
     * @param  list<int>  $authorIds
     * @return array<int, AuthorReadModel> keyed by author id
     */
    public function of(array $authorIds, ?int $viewerId): array;

    public function idOfUsername(string $username): ?int;
}
