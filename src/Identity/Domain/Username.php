<?php

namespace Conduit\Identity\Domain;

use Conduit\Identity\Domain\Exception\InvalidUsername;

final readonly class Username
{
    private const MAX_LENGTH = 50;

    public string $value;

    public function __construct(string $value)
    {
        $value = trim($value);

        if ($value === '' || mb_strlen($value) > self::MAX_LENGTH) {
            throw InvalidUsername::because($value);
        }

        $this->value = $value;
    }
}
