<?php

namespace Conduit\Article\Application\Port\Primary;

use Conduit\Article\Application\ReadModel\ArticleList;

interface ListArticles
{
    public function __invoke(ArticleFilter $filter, ?int $viewerId): ArticleList;
}
