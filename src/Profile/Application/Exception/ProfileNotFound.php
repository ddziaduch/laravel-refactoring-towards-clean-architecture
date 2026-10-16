<?php

namespace Conduit\Profile\Application\Exception;

use RuntimeException;

final class ProfileNotFound extends RuntimeException
{
    public static function withUsername(string $username): self
    {
        return new self(sprintf('Profile "%s" not found.', $username));
    }
}
