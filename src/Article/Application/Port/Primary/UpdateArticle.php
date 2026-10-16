<?php

namespace Conduit\Article\Application\Port\Primary;

use Conduit\Article\Application\Exception\ArticleNotFound;
use Conduit\Article\Domain\Exception\NotArticleAuthor;

interface UpdateArticle
{
    /**
     * @return string the slug of the article, which changes with its title
     *
     * @throws ArticleNotFound
     * @throws NotArticleAuthor
     */
    public function __invoke(string $slug, int $editorId, ArticleChanges $changes): string;
}
