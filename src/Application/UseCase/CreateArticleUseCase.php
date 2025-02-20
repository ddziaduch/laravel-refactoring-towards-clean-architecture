<?php

declare(strict_types=1);

namespace Clean\Application\UseCase;

use Clean\Application\Port\In\CreateArticleUseCasePort;
use Clean\Application\Port\Out\ArticleRepository;
use Clean\Application\Port\Out\Slugger;
use Clean\Domain\Entity\Article;

final class CreateArticleUseCase implements CreateArticleUseCasePort
{
    private Slugger $slugger;
    private ArticleRepository $repository;

    public function __construct(
        Slugger $slugger,
        ArticleRepository $repository
    ) {
        $this->slugger = $slugger;
        $this->repository = $repository;
    }

    public function __invoke(
        int $authorId,
        string $title,
        string $description,
        string $body,
        string ...$tagList
    ): int {
        $slug = $this->slugger->slugify($title);
        $article = new Article(
            $slug,
            $title,
            $description,
            $body,
            $authorId,
            ...$tagList,
        );
        $this->repository->save($article);

        return $article->id();
    }
}
