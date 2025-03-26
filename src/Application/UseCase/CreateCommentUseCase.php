<?php

namespace Clean\Application\UseCase;

use Clean\Application\Port\In\CreateCommentUseCasePort;
use Clean\Application\Port\Out\CommentRepository;
use Clean\Domain\Entity\Comment;

final class CreateCommentUseCase implements CreateCommentUseCasePort
{
    private CommentRepository $commentRepository;

    public function __construct(
        CommentRepository $commentRepository
    ) {
        $this->commentRepository = $commentRepository;
    }

    public function create(string $articleSlug, string $commentBody, int $authorId): int
    {
        $comment = new Comment($articleSlug, $commentBody, $authorId);

        $this->commentRepository->save($comment);

        $id = $comment->id();
        assert($id !== null);

        return $comment->id();
    }
}
