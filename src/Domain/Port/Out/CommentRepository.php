<?php

declare(strict_types=1);

namespace Clean\Domain\Port\Out;

use Clean\Domain\Entity\Comment;

interface CommentRepository
{
    public function createComment(Comment $comment): int;
}
