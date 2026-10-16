<?php

namespace Conduit\Identity\Application\Port\Secondary;

use Conduit\Identity\Domain\HashedPassword;

interface PasswordHasher
{
    public function hash(string $plainPassword): HashedPassword;

    public function verify(string $plainPassword, HashedPassword $hashedPassword): bool;
}
