<?php

namespace Clean\Application\UseCase;

use Clean\Application\Port\Secondary\CommentRepository;
use Clean\Domain\Entity\Comment;
use LogicException;

final readonly class CreateCommentUseCase
{
    public function __construct(
        private CommentRepository $commentRepository,
    ) {}

    public function __invoke(int $articleId, string $body, int $authorId): int
    {
        $comment = new Comment($articleId, $authorId, $body);

        $this->commentRepository->save($comment);

        return $comment->id() ?? throw new LogicException('The saved comment has no ID.');
    }
}
