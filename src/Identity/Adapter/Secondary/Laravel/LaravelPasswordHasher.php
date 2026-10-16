<?php

namespace Conduit\Identity\Adapter\Secondary\Laravel;

use Conduit\Identity\Application\Port\Secondary\PasswordHasher;
use Conduit\Identity\Domain\HashedPassword;
use Illuminate\Contracts\Hashing\Hasher;

final readonly class LaravelPasswordHasher implements PasswordHasher
{
    public function __construct(private Hasher $hasher) {}

    public function hash(string $plainPassword): HashedPassword
    {
        return new HashedPassword($this->hasher->make($plainPassword));
    }

    public function verify(string $plainPassword, HashedPassword $hashedPassword): bool
    {
        return $this->hasher->check($plainPassword, $hashedPassword->value);
    }
}
