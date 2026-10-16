<?php

namespace Conduit\Article\Application\UseCase;

use Conduit\Article\Application\Exception\ArticleNotFound;
use Conduit\Article\Application\Port\Primary\FavoriteArticle;
use Conduit\Article\Application\Port\Secondary\ArticleRepository;
use Conduit\Article\Application\Port\Secondary\FavoriteRepository;

final readonly class FavoriteArticleUseCase implements FavoriteArticle
{
    public function __construct(
        private ArticleRepository $articles,
        private FavoriteRepository $favorites,
    ) {}

    public function __invoke(string $slug, int $userId): void
    {
        $article = $this->articles->findBySlug($slug) ?? throw ArticleNotFound::withSlug($slug);

        $this->favorites->add($article->id, $userId);
    }
}
