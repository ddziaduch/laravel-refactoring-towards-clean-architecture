<?php

namespace Conduit\Article\Application\UseCase;

use Conduit\Article\Application\Exception\ArticleNotFound;
use Conduit\Article\Application\Port\Primary\GetArticle;
use Conduit\Article\Application\Port\Secondary\ArticleQueries;
use Conduit\Article\Application\ReadModel\ArticleReadModel;
use Conduit\Article\Application\Service\ArticleAssembler;

final readonly class GetArticleUseCase implements GetArticle
{
    public function __construct(
        private ArticleQueries $articles,
        private ArticleAssembler $assembler,
    ) {}

    public function __invoke(string $slug, ?int $viewerId): ArticleReadModel
    {
        $record = $this->articles->bySlug($slug, $viewerId) ?? throw ArticleNotFound::withSlug($slug);

        return $this->assembler->one($record, $viewerId);
    }
}
