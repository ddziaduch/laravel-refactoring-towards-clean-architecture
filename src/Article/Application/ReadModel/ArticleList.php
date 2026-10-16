<?php

namespace Conduit\Article\Application\ReadModel;

final readonly class ArticleList
{
    /**
     * @param  list<ArticleReadModel>  $articles
     */
    public function __construct(
        public array $articles,
        public int $count,
    ) {}

    public static function empty(): self
    {
        return new self([], 0);
    }
}
