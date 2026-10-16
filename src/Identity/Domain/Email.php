<?php

namespace Conduit\Identity\Domain;

use Conduit\Identity\Domain\Exception\InvalidEmail;

final readonly class Email
{
    public string $value;

    public function __construct(string $value)
    {
        $value = trim($value);

        if (! str_contains($value, '@')) {
            throw InvalidEmail::because($value);
        }

        $this->value = $value;
    }
}
