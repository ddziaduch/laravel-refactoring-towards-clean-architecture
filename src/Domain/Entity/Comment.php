<?php

namespace Clean\Domain\Entity;

use Clean\Domain\Exception\CommentBodyCannotBeEmpty;
use Clean\Domain\Exception\OnlyAuthorCanDeleteComment;
use DateTimeImmutable;

final class Comment
{
    public function __construct(
        public readonly int $articleId,
        public readonly int $authorId,
        public readonly string $body,
        public readonly ?int $id = null,
        private ?DateTimeImmutable $deletedAt = null,
    ) {
        if (trim($body) === '') {
            throw new CommentBodyCannotBeEmpty;
        }
    }

    /**
     * @throws OnlyAuthorCanDeleteComment
     */
    public function deleteBy(int $userId, DateTimeImmutable $now): void
    {
        if ($userId !== $this->authorId) {
            throw new OnlyAuthorCanDeleteComment;
        }

        $this->deletedAt = $now;
    }

    public function deletedAt(): ?DateTimeImmutable
    {
        return $this->deletedAt;
    }

    public function isDeleted(): bool
    {
        return $this->deletedAt !== null;
    }
}
