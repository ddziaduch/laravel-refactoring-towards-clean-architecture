<?php

namespace Conduit\Article\Application\UseCase;

use Conduit\Article\Application\Exception\ArticleNotFound;
use Conduit\Article\Application\Port\Primary\DeleteArticle;
use Conduit\Article\Application\Port\Secondary\ArticleRepository;

final readonly class DeleteArticleUseCase implements DeleteArticle
{
    public function __construct(private ArticleRepository $articles) {}

    public function __invoke(string $slug, int $userId): void
    {
        $article = $this->articles->findBySlug($slug) ?? throw ArticleNotFound::withSlug($slug);
        $article->assertAuthoredBy($userId);

        $this->articles->remove($article);
    }
}
