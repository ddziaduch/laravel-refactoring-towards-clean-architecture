<?php

namespace Clean\Application\Port\Secondary;

use Clean\Domain\Entity\Comment;

interface CommentRepository
{
    public function save(Comment $comment): void;
}
