<?php

namespace Conduit\Engagement\Application\Port\Secondary;

use DateTimeImmutable;

final readonly class CommentRecord
{
    public function __construct(
        public int $id,
        public DateTimeImmutable $createdAt,
        public DateTimeImmutable $updatedAt,
        public string $body,
        public int $authorId,
    ) {}
}
