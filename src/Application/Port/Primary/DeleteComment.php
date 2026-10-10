<?php

namespace Clean\Application\Port\Primary;

use Clean\Application\Exception\ArticleNotFound;
use Clean\Application\Exception\CommentNotFound;
use Clean\Domain\Exception\OnlyAuthorCanDeleteComment;

interface DeleteComment
{
    /**
     * @throws ArticleNotFound
     * @throws CommentNotFound
     * @throws OnlyAuthorCanDeleteComment
     */
    public function __invoke(string $articleSlug, int $commentId, int $userId): void;
}
