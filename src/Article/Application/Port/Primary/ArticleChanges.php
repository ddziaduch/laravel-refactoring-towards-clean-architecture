<?php

namespace Conduit\Article\Application\Port\Primary;

/**
 * Null means "leave unchanged".
 */
final readonly class ArticleChanges
{
    /**
     * @param  list<string>|null  $tagList
     */
    public function __construct(
        public ?string $title = null,
        public ?string $description = null,
        public ?string $body = null,
        public ?array $tagList = null,
    ) {}
}
