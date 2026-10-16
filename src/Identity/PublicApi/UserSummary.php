<?php

namespace Conduit\Identity\PublicApi;

final readonly class UserSummary
{
    public function __construct(
        public int $id,
        public string $username,
        public ?string $bio,
        public ?string $image,
    ) {}
}
