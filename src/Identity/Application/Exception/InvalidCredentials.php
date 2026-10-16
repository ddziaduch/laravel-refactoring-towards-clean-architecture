<?php

namespace Conduit\Identity\Application\Exception;

use RuntimeException;

final class InvalidCredentials extends RuntimeException
{
    public function __construct()
    {
        parent::__construct('Invalid email or password.');
    }
}
