<?php

declare(strict_types=1);

namespace Clean\Application\Port\Outgoing;

interface CommentRepository
{
    public function save(string $slug, string $body, int $authorId): int;
}
