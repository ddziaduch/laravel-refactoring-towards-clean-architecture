<?php

namespace Conduit\Article\Application\Port\Secondary;

/**
 * Filters expressed in the module's own terms: ids instead of usernames.
 */
final readonly class ArticleCriteria
{
    /**
     * @param  list<int>|null  $authorIds  null means any author
     */
    public function __construct(
        public ?string $tag = null,
        public ?array $authorIds = null,
        public ?int $favoritedBy = null,
    ) {}
}
