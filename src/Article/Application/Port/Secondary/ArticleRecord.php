<?php

namespace Conduit\Article\Application\Port\Secondary;

use DateTimeImmutable;

final readonly class ArticleRecord
{
    /**
     * @param  list<string>  $tagList
     */
    public function __construct(
        public string $slug,
        public string $title,
        public string $description,
        public string $body,
        public array $tagList,
        public DateTimeImmutable $createdAt,
        public DateTimeImmutable $updatedAt,
        public bool $favorited,
        public int $favoritesCount,
        public int $authorId,
    ) {}
}
