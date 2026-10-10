<?php

namespace Clean\Application\UseCase;

use Clean\Application\Exception\ArticleNotFound;
use Clean\Application\Exception\CommentNotFound;
use Clean\Application\Port\Primary\DeleteComment;
use Clean\Application\Port\Secondary\ArticleRepository;
use Clean\Application\Port\Secondary\CommentRepository;

final readonly class DeleteCommentUseCase implements DeleteComment
{
    public function __construct(
        private ArticleRepository $articleRepository,
        private CommentRepository $commentRepository,
    ) {}

    public function __invoke(string $articleSlug, int $commentId, int $userId): void
    {
        $articleId = $this->articleRepository->findIdBySlug($articleSlug)
            ?? throw ArticleNotFound::withSlug($articleSlug);

        $comment = $this->commentRepository->find($commentId);

        if ($comment === null || $comment->articleId !== $articleId) {
            throw CommentNotFound::withId($commentId);
        }

        $comment->ensureCanBeDeletedBy($userId);

        $this->commentRepository->delete($commentId);
    }
}
