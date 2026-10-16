<?php

namespace Conduit\Identity\Application\ReadModel;

final readonly class AuthenticatedUser
{
    public function __construct(
        public UserReadModel $user,
        public string $token,
    ) {}
}
