<?php

declare(strict_types=1);

namespace Clean\Application\ReadModel;

final class ArticleReadModel
{
    public function __construct(
        public readonly int $id,
        public readonly string $slug,
        public readonly string $title,
        public readonly string $description,
        public readonly string $body,
        /**
         * @var string[]
         */
        public readonly array $tagList,
        public readonly \DateTime $createdAt,
        public readonly \DateTime $updatedAt,
        public readonly int $favoritesCount,
        public readonly ProfileReadModel $author,
    ) {
    }
}
