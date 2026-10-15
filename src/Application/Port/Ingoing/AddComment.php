<?php
declare(strict_types=1);

namespace Clean\Application\Port\Ingoing;

interface AddComment
{
    public function add(string $articleSlug, string $body, int $authorId): int;
}
