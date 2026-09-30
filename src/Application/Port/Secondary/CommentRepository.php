<?php

namespace Clean\Application\Port\Secondary;

interface CommentRepository
{
    public function save(int $articleId, string $body, int $authorId): int;
}
