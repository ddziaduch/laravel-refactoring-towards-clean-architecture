<?php

namespace Clean\Application\UseCase;

use Clean\Application\Port\Out\CommentRepository;
use Clean\Application\Port\Out\GetCommentReadModel;
use Clean\Application\Port\UseCase\CreateCommentUseCasePort;
use Clean\Application\ReadModel\CommentReadModel;
use Clean\Domain\Entity\Comment;

final class CreateCommentUseCase implements CreateCommentUseCasePort
{
    private CommentRepository $commentRepository;
    private GetCommentReadModel $getCommentReadModel;

    public function __construct(
        CommentRepository $commentRepository,
        GetCommentReadModel $getCommentReadModel
    ) {
        $this->getCommentReadModel = $getCommentReadModel;
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
