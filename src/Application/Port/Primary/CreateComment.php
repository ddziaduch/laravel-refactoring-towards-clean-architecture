<?php

namespace Clean\Application\Port\Primary;

use Clean\Application\Exception\ArticleNotFound;

interface CreateComment
{
    /**
     * @throws ArticleNotFound
     */
    public function __invoke(string $articleSlug, string $body, int $authorId): int;
}
