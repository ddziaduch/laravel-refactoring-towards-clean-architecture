<?php

namespace Conduit\Article\Application\Port\Primary;

use Conduit\Article\Application\ReadModel\ArticleList;

interface GetFeed
{
    public function __invoke(int $readerId, int $limit, int $offset): ArticleList;
}
