<?php

namespace Clean\Application\UseCase;

use Clean\Application\Port\Out\CommentRepository;
use Clean\Application\Port\UseCase\CreateCommentUseCasePort;
use Clean\Domain\Entity\Comment;

final class CreateCommentUseCase implements CreateCommentUseCasePort
{
    public function __construct(
        private readonly CommentRepository $commentRepository,
    ) {
    }

    public function create(int $articleId, string $commentBody, int $authorId): int
    {
        $comment = new Comment($articleId, $commentBody, $authorId);

        $this->commentRepository->save($comment);

        return $comment->id();
    }
}
