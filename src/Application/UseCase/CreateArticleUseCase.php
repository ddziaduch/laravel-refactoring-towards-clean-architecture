<?php

declare(strict_types=1);

namespace Clean\Application\UseCase;

use Clean\Application\Port\In\CreateArticleUseCasePort;
use Clean\Application\Port\Out\ArticleReadModelFinder;
use Clean\Application\Port\Out\ArticleRepository;
use Clean\Application\Port\Out\Slugger;
use Clean\Application\ReadModel\ArticleReadModel;
use Clean\Domain\Entity\Article;

final class CreateArticleUseCase implements CreateArticleUseCasePort
{
    private Slugger $slugger;
    private ArticleRepository $repository;
    private ArticleReadModelFinder $readModelFinder;

    public function __construct(
        Slugger $slugger,
        ArticleRepository $repository,
        ArticleReadModelFinder $readModelFinder
    ) {
        $this->readModelFinder = $readModelFinder;
        $this->repository = $repository;
        $this->slugger = $slugger;
    }

    public function __invoke(
        int $authorId,
        string $title,
        string $description,
        string $body,
        string ...$tagList
    ): ArticleReadModel {
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

        return $this->readModelFinder->bySlug($slug);
    }
}
