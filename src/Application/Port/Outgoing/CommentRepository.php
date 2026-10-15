<?php

declare(strict_types=1);

namespace Clean\Application\Port\Outgoing;

use Clean\Domain\Entity\Comment;

interface CommentRepository
{
    public function save(Comment $comment): int;
}
