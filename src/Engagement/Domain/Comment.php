<?php

namespace Conduit\Engagement\Domain;

use Conduit\Engagement\Domain\Exception\CommentBodyCannotBeEmpty;
use Conduit\Engagement\Domain\Exception\NotCommentAuthor;

/**
 * A comment knows its article and author by id only: both belong to other modules.
 */
final readonly class Comment
{
    public function __construct(
        public ?int $id,
        public int $articleId,
        public int $authorId,
        public string $body,
    ) {
        if (trim($body) === '') {
            throw new CommentBodyCannotBeEmpty;
        }
    }

    public static function write(int $articleId, int $authorId, string $body): self
    {
        return new self(null, $articleId, $authorId, $body);
    }

    public function belongsTo(int $articleId): bool
    {
        return $this->articleId === $articleId;
    }

    /**
     * @throws NotCommentAuthor
     */
    public function assertAuthoredBy(int $userId): void
    {
        if ($userId !== $this->authorId) {
            throw NotCommentAuthor::of($this->id, $userId);
        }
    }
}
