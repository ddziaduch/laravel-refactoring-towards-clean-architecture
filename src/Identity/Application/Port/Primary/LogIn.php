<?php

namespace Conduit\Identity\Application\Port\Primary;

use Conduit\Identity\Application\Exception\InvalidCredentials;
use Conduit\Identity\Application\ReadModel\AuthenticatedUser;

interface LogIn
{
    /**
     * @throws InvalidCredentials
     */
    public function __invoke(string $email, string $password): AuthenticatedUser;
}
