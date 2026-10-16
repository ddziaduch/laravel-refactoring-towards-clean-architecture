<?php

namespace Conduit\Profile\Application\Port\Secondary;

final readonly class UserAccount
{
    public function __construct(
        public int $id,
        public string $username,
        public ?string $bio,
        public ?string $image,
    ) {}
}
