<?php

namespace Conduit\Profile\Application\Port\Primary;

use Conduit\Profile\Application\Exception\ProfileNotFound;
use Conduit\Profile\Application\ReadModel\ProfileReadModel;

interface GetProfile
{
    /**
     * @throws ProfileNotFound
     */
    public function __invoke(string $username, ?int $viewerId): ProfileReadModel;
}
