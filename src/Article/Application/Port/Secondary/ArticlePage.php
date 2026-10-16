<?php

namespace Conduit\Article\Application\Port\Secondary;

final readonly class ArticlePage
{
    /**
     * @param  list<ArticleRecord>  $records
     */
    public function __construct(
        public array $records,
        public int $total,
    ) {}
}
