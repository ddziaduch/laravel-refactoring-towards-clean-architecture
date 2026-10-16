<?php

namespace Conduit\Article\Application\UseCase;

use Conduit\Article\Application\Port\Primary\ArticleFilter;
use Conduit\Article\Application\Port\Primary\ListArticles;
use Conduit\Article\Application\Port\Secondary\ArticleCriteria;
use Conduit\Article\Application\Port\Secondary\ArticleQueries;
use Conduit\Article\Application\Port\Secondary\AuthorProfiles;
use Conduit\Article\Application\ReadModel\ArticleList;
use Conduit\Article\Application\Service\ArticleAssembler;

final readonly class ListArticlesUseCase implements ListArticles
{
    public function __construct(
        private ArticleQueries $articles,
        private AuthorProfiles $authorProfiles,
        private ArticleAssembler $assembler,
    ) {}

    public function __invoke(ArticleFilter $filter, ?int $viewerId): ArticleList
    {
        // Usernames belong to another module, so they are translated to ids
        // before the query. An unknown user simply matches no article.
        $authorIds = null;

        if ($filter->author !== null) {
            $authorId = $this->authorProfiles->idOfUsername($filter->author);

            if ($authorId === null) {
                return ArticleList::empty();
            }

            $authorIds = [$authorId];
        }

        $favoritedBy = null;

        if ($filter->favoritedBy !== null) {
            $favoritedBy = $this->authorProfiles->idOfUsername($filter->favoritedBy);

            if ($favoritedBy === null) {
                return ArticleList::empty();
            }
        }

        $page = $this->articles->page(
            new ArticleCriteria($filter->tag, $authorIds, $favoritedBy),
            $filter->limit,
            $filter->offset,
            $viewerId,
        );

        return $this->assembler->list($page, $viewerId);
    }
}
