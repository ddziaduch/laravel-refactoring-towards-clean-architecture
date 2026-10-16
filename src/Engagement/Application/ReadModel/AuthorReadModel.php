<?php

namespace Conduit\Engagement\Application\ReadModel;

/**
 * Deliberately a copy of the Article module's author read model: modules do not
 * share internals, even when they look alike today.
 */
final readonly class AuthorReadModel
{
    public function __construct(
        public string $username,
        public ?string $bio,
        public ?string $image,
        public bool $following,
    ) {}
}
