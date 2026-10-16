<?php

namespace Conduit\Profile\Application\Port\Primary;

use Conduit\Profile\Application\Exception\ProfileNotFound;
use Conduit\Profile\Application\ReadModel\ProfileReadModel;

interface FollowUser
{
    /**
     * @throws ProfileNotFound
     */
    public function __invoke(int $followerId, string $username): ProfileReadModel;
}
