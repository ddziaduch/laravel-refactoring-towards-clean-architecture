<?php

namespace Clean\Application\Port\Secondary;

use Clean\Domain\Entity\Comment;

interface CommentRepository
{
    public function find(int $commentId): ?Comment;

    public function save(Comment $comment): int;

    public function delete(int $commentId): void;
}
