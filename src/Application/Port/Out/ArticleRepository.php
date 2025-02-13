<?php

declare(strict_types = 1);

namespace Clean\Application\Port\Out;

use Clean\Application\Exception\ArticleDoesNotExist;

interface ArticleRepository
{
    /**
     * @throws ArticleDoesNotExist
     */
    public function delete(int $authorId, string $articleSlug): void;
}
