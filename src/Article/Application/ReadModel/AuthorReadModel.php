<?php

namespace Conduit\Article\Application\ReadModel;

final readonly class AuthorReadModel
{
    public function __construct(
        public string $username,
        public ?string $bio,
        public ?string $image,
        public bool $following,
    ) {}
}
