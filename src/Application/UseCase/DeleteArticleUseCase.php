<?php

declare(strict_types = 1);

namespace Clean\Application\UseCase;

use Clean\Application\Exception\ArticleDoesNotExist;
use Clean\Application\Exception\GivenUserIsNotAnAuthorOfTheArticle;
use Clean\Application\Port\Out\ArticleRepository;
use Clean\Application\Port\In\DeleteArticleUseCasePort;

final class DeleteArticleUseCase implements DeleteArticleUseCasePort
{
    private ArticleRepository $articleRepository;

    public function __construct(
        ArticleRepository $articleRepository
    ) {
        $this->articleRepository = $articleRepository;
    }

    /**
     * @throws ArticleDoesNotExist
     */
    public function handle(int $authorId, string $articleSlug): void
    {
        $article = $this->articleRepository->getBySlug($articleSlug);
        if (!$article->wasCreatedBy($authorId)) {
            throw new GivenUserIsNotAnAuthorOfTheArticle($authorId, $articleSlug);
        }
        $article->markAsRemoved();
        $this->articleRepository->save($article);
    }
}
