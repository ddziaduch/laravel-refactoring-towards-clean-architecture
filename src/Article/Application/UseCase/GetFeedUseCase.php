<?php

namespace Conduit\Article\Application\UseCase;

use Conduit\Article\Application\Port\Primary\GetFeed;
use Conduit\Article\Application\Port\Secondary\ArticleCriteria;
use Conduit\Article\Application\Port\Secondary\ArticleQueries;
use Conduit\Article\Application\Port\Secondary\Followings;
use Conduit\Article\Application\ReadModel\ArticleList;
use Conduit\Article\Application\Service\ArticleAssembler;

final readonly class GetFeedUseCase implements GetFeed
{
    public function __construct(
        private Followings $followings,
        private ArticleQueries $articles,
        private ArticleAssembler $assembler,
    ) {}

    public function __invoke(int $readerId, int $limit, int $offset): ArticleList
    {
        $authorIds = $this->followings->followedBy($readerId);

        if ($authorIds === []) {
            return ArticleList::empty();
        }

        $page = $this->articles->page(new ArticleCriteria(authorIds: $authorIds), $limit, $offset, $readerId);

        return $this->assembler->list($page, $readerId);
    }
}
