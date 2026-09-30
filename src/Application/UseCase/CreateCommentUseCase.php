<?php

namespace Clean\Application\UseCase;

use Clean\Application\Exception\ArticleNotFound;
use Clean\Application\Port\Primary\CreateComment;
use Clean\Application\Port\Secondary\ArticleRepository;
use Clean\Application\Port\Secondary\CommentRepository;
use Clean\Domain\Entity\Comment;

final readonly class CreateCommentUseCase implements CreateComment
{
    public function __construct(
        private ArticleRepository $articleRepository,
        private CommentRepository $commentRepository,
    ) {}

    /**
     * @throws ArticleNotFound
     */
    public function __invoke(string $articleSlug, string $body, int $authorId): int
    {
        $articleId = $this->articleRepository->findIdBySlug($articleSlug)
            ?? throw ArticleNotFound::withSlug($articleSlug);

        return $this->commentRepository->save(new Comment($articleId, $authorId, $body));
    }
}
