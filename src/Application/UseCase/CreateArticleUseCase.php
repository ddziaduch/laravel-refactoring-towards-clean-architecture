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

    public function __construct(
        private readonly Slugger $slugger,
        private readonly ArticleRepository $repository,
        private readonly ArticleReadModelFinder $readModelFinder,
    ) {
    }

    public function __invoke(
        int $authorId,
        string $title,
        string $description,
        string $body,
        string ...$tagList,
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
