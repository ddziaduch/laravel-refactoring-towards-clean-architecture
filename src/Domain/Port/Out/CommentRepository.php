<?php

declare(strict_types=1);

namespace Clean\Domain\Port\Out;

use Clean\Domain\Entity\Comment;
use Clean\Domain\Exception\ArticleNotFound;

interface CommentRepository
{
    /**
     * @throws ArticleNotFound
     */
    public function createComment(Comment $comment): int;
}
