<?php

namespace Conduit\Identity\Domain\Exception;

use DomainException;

final class InvalidEmail extends DomainException
{
    public static function because(string $email): self
    {
        return new self(sprintf('"%s" is not a valid email address.', $email));
    }
}
