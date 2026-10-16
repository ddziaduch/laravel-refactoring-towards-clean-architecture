<?php

namespace Conduit\Profile\Domain;

/**
 * One user following another one. Users are referenced by id only:
 * the Profile module does not own them.
 */
final readonly class Follow
{
    public function __construct(
        public int $followerId,
        public int $followedId,
    ) {}
}
