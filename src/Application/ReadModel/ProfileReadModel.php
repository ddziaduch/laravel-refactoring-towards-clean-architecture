<?php

namespace Clean\Application\ReadModel;

final readonly class ProfileReadModel
{
    public function __construct(
        public string $username,
        public ?string $bio,
        public ?string $image,
        public bool $following,
    ) {}
}
