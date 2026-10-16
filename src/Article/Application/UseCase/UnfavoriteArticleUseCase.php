<?php

namespace Conduit\Article\Application\UseCase;

use Conduit\Article\Application\Exception\ArticleNotFound;
use Conduit\Article\Application\Port\Primary\UnfavoriteArticle;
use Conduit\Article\Application\Port\Secondary\ArticleRepository;
use Conduit\Article\Application\Port\Secondary\FavoriteRepository;

final readonly class UnfavoriteArticleUseCase implements UnfavoriteArticle
{
    public function __construct(
        private ArticleRepository $articles,
        private FavoriteRepository $favorites,
    ) {}

    public function __invoke(string $slug, int $userId): void
    {
        $article = $this->articles->findBySlug($slug) ?? throw ArticleNotFound::withSlug($slug);

        $this->favorites->remove($article->id, $userId);
    }
}
