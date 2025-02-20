<?php

declare(strict_types = 1);

namespace Clean\Application\UseCase;

use Clean\Application\Exception\ArticleDoesNotExist;
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
        // this use case is very simple, but it could be extended
        // for example a domain event could be dispatched
        // on such event multiple things could happen
        // notifications could be sent etc
        $this->articleRepository->delete($authorId, $articleSlug);
    }
}
