<?php

namespace Conduit\Article\Application\Port\Primary;

final readonly class ArticleFilter
{
    public function __construct(
        public ?string $tag = null,
        public ?string $author = null,
        public ?string $favoritedBy = null,
        public int $limit = 20,
        public int $offset = 0,
    ) {}
}
