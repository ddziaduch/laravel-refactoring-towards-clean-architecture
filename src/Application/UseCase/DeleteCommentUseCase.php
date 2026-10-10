<?php

namespace Clean\Application\UseCase;

use Clean\Application\Exception\ArticleNotFound;
use Clean\Application\Exception\CommentNotFound;
use Clean\Application\Port\Primary\DeleteComment;
use Clean\Application\Port\Secondary\ArticleRepository;
use Clean\Application\Port\Secondary\Clock;
use Clean\Application\Port\Secondary\CommentRepository;

final readonly class DeleteCommentUseCase implements DeleteComment
{
    public function __construct(
        private ArticleRepository $articleRepository,
        private CommentRepository $commentRepository,
        private Clock $clock,
    ) {}

    public function __invoke(string $articleSlug, int $commentId, int $userId): void
    {
        $articleId = $this->articleRepository->findIdBySlug($articleSlug)
            ?? throw ArticleNotFound::withSlug($articleSlug);

        $comment = $this->commentRepository->find($commentId);

        if ($comment === null || $comment->isDeleted() || $comment->articleId !== $articleId) {
            throw CommentNotFound::withId($commentId);
        }

        $comment->deleteBy($userId, $this->clock->now());

        $this->commentRepository->save($comment);
    }
}
