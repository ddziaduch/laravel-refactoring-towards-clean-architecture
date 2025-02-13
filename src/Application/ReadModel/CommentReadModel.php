<?php

namespace Clean\Application\ReadModel;

final class CommentReadModel
{
    public function __construct(
        public readonly int $id,
        public readonly \DateTime $createdAt,
        public readonly \DateTime $updatedAt,
        public readonly string $body,
        public readonly ProfileReadModel $author,
    ) {
    }
}
