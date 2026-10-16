<?php

namespace Conduit\Identity\Application\Port\Primary;

use Conduit\Identity\Application\Exception\UserNotFound;
use Conduit\Identity\Application\ReadModel\UserReadModel;

interface GetUser
{
    /**
     * @throws UserNotFound
     */
    public function __invoke(int $userId): UserReadModel;
}
