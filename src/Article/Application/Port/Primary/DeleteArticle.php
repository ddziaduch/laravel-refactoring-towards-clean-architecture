<?php

namespace Conduit\Article\Application\Port\Primary;

use Conduit\Article\Application\Exception\ArticleNotFound;
use Conduit\Article\Domain\Exception\NotArticleAuthor;

interface DeleteArticle
{
    /**
     * @throws ArticleNotFound
     * @throws NotArticleAuthor
     */
    public function __invoke(string $slug, int $userId): void;
}
