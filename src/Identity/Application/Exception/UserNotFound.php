<?php

namespace Conduit\Identity\Application\Exception;

use RuntimeException;

final class UserNotFound extends RuntimeException
{
    public static function withId(int $id): self
    {
        return new self(sprintf('User #%d not found.', $id));
    }
}
