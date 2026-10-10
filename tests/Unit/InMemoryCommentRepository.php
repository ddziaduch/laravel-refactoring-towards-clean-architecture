<?php

namespace Tests\Unit;

use Clean\Application\Port\Secondary\CommentRepository;
use Clean\Domain\Entity\Comment;

final class InMemoryCommentRepository implements CommentRepository
{
    /**
     * @var array<int, Comment>
     */
    public array $saved = [];

    private int $lastId = 0;

    public function find(int $commentId): ?Comment
    {
        return $this->saved[$commentId] ?? null;
    }

    public function save(Comment $comment): int
    {
        if ($comment->id === null) {
            $comment = new Comment($comment->articleId, $comment->authorId, $comment->body, ++$this->lastId);
        }

        $this->saved[$comment->id] = $comment;

        return $comment->id;
    }
}
