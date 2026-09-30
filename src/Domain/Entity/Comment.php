<?php

namespace Clean\Domain\Entity;

use Clean\Domain\Exception\CommentBodyCannotBeEmpty;

final readonly class Comment
{
    public function __construct(
        public int $articleId,
        public int $authorId,
        public string $body,
    ) {
        if (trim($body) === '') {
            throw new CommentBodyCannotBeEmpty;
        }
    }
}
