<?php

namespace Clean\Domain\Entity;

use Clean\Domain\Exception\CommentBodyCannotBeEmpty;

final class Comment
{
    private ?int $id = null;

    public function __construct(
        public readonly int $articleId,
        public readonly int $authorId,
        public readonly string $body,
    ) {
        if (trim($body) === '') {
            throw new CommentBodyCannotBeEmpty;
        }
    }

    public function id(): ?int
    {
        return $this->id;
    }
}
