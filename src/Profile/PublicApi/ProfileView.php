<?php

namespace Conduit\Profile\PublicApi;

final readonly class ProfileView
{
    public function __construct(
        public int $userId,
        public string $username,
        public ?string $bio,
        public ?string $image,
        public bool $following,
    ) {}
}
