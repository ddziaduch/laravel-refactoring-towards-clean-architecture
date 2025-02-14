<?php

declare(strict_types=1);

namespace Clean\Application\ReadModel;

final class ProfileReadModel
{
    public function __construct(
        public readonly string $username,
        public readonly ?string $bio,
        public readonly ?string $image,
        // todo: rethink this, not sure whether it should be here
        public readonly bool $following,
    ) {
    }
}
