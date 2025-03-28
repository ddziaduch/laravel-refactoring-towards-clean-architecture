<?php

declare(strict_types=1);

namespace Clean\Application;

use Clean\Application\Port\In\CreateCommentUserCaseInterface;
use Clean\Domain\Entity\Comment;
use Clean\Domain\Port\Out\CommentRepository;

final class CreateCommentUseCase implements CreateCommentUserCaseInterface
{
    private CommentRepository $commentRepository;

    public function __construct(CommentRepository $commentRepository)
    {
        $this->commentRepository = $commentRepository;
    }

    public function __invoke(
        string $articleSlug,
        int $authorId,
        string $commentBody
    ): int {
        $comment = Comment::new(
            $articleSlug,
            $authorId,
            $commentBody
        );
        $id = $this->commentRepository->createComment($comment);
        $comment = $comment->withId($id);

        return $comment->getId();
    }
}
