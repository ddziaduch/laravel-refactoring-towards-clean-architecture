<?php

declare(strict_types=1);

namespace Clean\Application\Port\Outgoing;

interface ArticleRepository
{
    public function existsBySlug(string $articleSlug): bool;
}
