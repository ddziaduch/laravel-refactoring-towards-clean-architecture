<?php

namespace Clean\Application\Port\Secondary;

use Clean\Domain\Entity\Comment;

interface CommentRepository
{
    public function find(int $commentId): ?Comment;

    /**
     * Inserts a new comment or updates an existing one, and returns its ID.
     */
    public function save(Comment $comment): int;
}
