<?php

namespace Clean\Domain\Entity;

use Clean\Domain\Exception\CommentBodyCannotBeEmpty;
use Clean\Domain\Exception\OnlyAuthorCanDeleteComment;

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

    /**
     * @throws OnlyAuthorCanDeleteComment
     */
    public function ensureCanBeDeletedBy(int $userId): void
    {
        if ($userId !== $this->authorId) {
            throw new OnlyAuthorCanDeleteComment;
        }
    }
}
