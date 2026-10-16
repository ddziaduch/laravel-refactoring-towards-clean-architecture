<?php

namespace Conduit\Engagement\Application\Port\Secondary;

use Conduit\Engagement\Application\ReadModel\AuthorReadModel;

interface AuthorProfiles
{
    /**
     * @param  list<int>  $authorIds
     * @return array<int, AuthorReadModel> keyed by author id
     */
    public function of(array $authorIds, ?int $viewerId): array;
}
