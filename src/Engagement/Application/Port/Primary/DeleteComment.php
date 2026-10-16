<?php

namespace Conduit\Engagement\Application\Port\Primary;

use Conduit\Engagement\Application\Exception\ArticleNotFound;
use Conduit\Engagement\Application\Exception\CommentNotFound;
use Conduit\Engagement\Domain\Exception\NotCommentAuthor;

interface DeleteComment
{
    /**
     * @throws ArticleNotFound
     * @throws CommentNotFound when the comment does not exist or belongs to another article
     * @throws NotCommentAuthor
     */
    public function __invoke(string $articleSlug, int $commentId, int $userId): void;
}
