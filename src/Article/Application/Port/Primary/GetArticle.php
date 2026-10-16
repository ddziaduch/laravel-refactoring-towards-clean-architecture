<?php

namespace Conduit\Article\Application\Port\Primary;

use Conduit\Article\Application\Exception\ArticleNotFound;
use Conduit\Article\Application\ReadModel\ArticleReadModel;

interface GetArticle
{
    /**
     * @throws ArticleNotFound
     */
    public function __invoke(string $slug, ?int $viewerId): ArticleReadModel;
}
