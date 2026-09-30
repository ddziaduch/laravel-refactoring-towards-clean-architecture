<?php

namespace Clean\Application\UseCase;

use Clean\Application\Port\Secondary\CommentRepository;
use Clean\Domain\Entity\Comment;

final readonly class CreateCommentUseCase
{
    public function __construct(
        private CommentRepository $commentRepository,
    ) {}

    public function __invoke(int $articleId, string $body, int $authorId): int
    {
        return $this->commentRepository->save(new Comment($articleId, $authorId, $body));
    }
}
