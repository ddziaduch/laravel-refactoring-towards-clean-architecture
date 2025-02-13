<?php

namespace Clean\Application\UseCase;

use Clean\Application\Port\Out\CommentRepository;
use Clean\Application\Port\Out\GetCommentReadModel;
use Clean\Application\Port\UseCase\CreateCommentUseCasePort;
use Clean\Application\ReadModel\CommentReadModel;
use Clean\Domain\Entity\Comment;

final class CreateCommentUseCase implements CreateCommentUseCasePort
{
    public function __construct(
        private readonly CommentRepository $commentRepository,
        private readonly GetCommentReadModel $getCommentReadModel
    ) {
    }

    public function create(string $articleSlug, string $commentBody, int $authorId): CommentReadModel
    {
        $comment = new Comment($articleSlug, $commentBody, $authorId);

        $this->commentRepository->save($comment);

        return $this->getCommentReadModel->get($comment->id());
    }
}
