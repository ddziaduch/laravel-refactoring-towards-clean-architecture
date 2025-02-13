<?php

namespace Clean\Application\ReadModel;

class ProfileReadModel
{
    public function __construct(
        public readonly string $username,
        public readonly ?string $bio,
        public readonly ?string $image,
        public readonly bool $following,
    ) {
    }
}
