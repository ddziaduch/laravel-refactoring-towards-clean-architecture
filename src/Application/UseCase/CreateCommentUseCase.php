<?php

namespace Clean\Application\UseCase;

use Clean\Application\Port\Secondary\CommentRepository;

final readonly class CreateCommentUseCase
{
    public function __construct(
        private CommentRepository $commentRepository,
    ) {}

    public function __invoke(int $articleId, string $body, int $authorId): int
    {
        return $this->commentRepository->save($articleId, $body, $authorId);
    }
}
