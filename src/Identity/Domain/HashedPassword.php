<?php

namespace Conduit\Identity\Domain;

/**
 * The domain never sees a plain-text password, only its hash.
 */
final readonly class HashedPassword
{
    public function __construct(public string $value) {}
}
