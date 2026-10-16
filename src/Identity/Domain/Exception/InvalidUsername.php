<?php

namespace Conduit\Identity\Domain\Exception;

use DomainException;

final class InvalidUsername extends DomainException
{
    public static function because(string $username): self
    {
        return new self(sprintf('Username "%s" must have between 1 and 50 characters.', $username));
    }
}
