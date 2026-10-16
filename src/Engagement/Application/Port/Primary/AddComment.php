<?php

namespace Conduit\Engagement\Application\Port\Primary;

use Conduit\Engagement\Application\Exception\ArticleNotFound;
use Conduit\Engagement\Domain\Exception\CommentBodyCannotBeEmpty;

interface AddComment
{
    /**
     * @return int the id of the new comment
     *
     * @throws ArticleNotFound
     * @throws CommentBodyCannotBeEmpty
     */
    public function __invoke(string $articleSlug, string $body, int $authorId): int;
}
