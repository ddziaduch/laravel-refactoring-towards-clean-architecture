<?php

declare(strict_types=1);

namespace Clean\Application\ReadModel;

final class ProfileReadModel
{
    public function __construct(
        public readonly int $id,
        public readonly string $username,
        public readonly ?string $bio,
        public readonly ?string $image,
    ) {
    }
}
