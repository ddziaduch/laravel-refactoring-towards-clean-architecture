<?php

namespace Conduit\Engagement\Application\Port\Secondary;

use Conduit\Engagement\Domain\Comment;

interface CommentRepository
{
    public function add(Comment $comment): int;

    public function find(int $id): ?Comment;

    public function remove(Comment $comment): void;
}
