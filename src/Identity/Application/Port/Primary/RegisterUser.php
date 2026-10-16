<?php

namespace Conduit\Identity\Application\Port\Primary;

use Conduit\Identity\Application\ReadModel\AuthenticatedUser;

interface RegisterUser
{
    public function __invoke(string $username, string $email, string $password): AuthenticatedUser;
}
