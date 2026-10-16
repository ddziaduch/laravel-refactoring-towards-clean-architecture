<?php

namespace Conduit\Profile\Application\ReadModel;

final readonly class ProfileReadModel
{
    public function __construct(
        public int $userId,
        public string $username,
        public ?string $bio,
        public ?string $image,
        public bool $following,
    ) {}
}
