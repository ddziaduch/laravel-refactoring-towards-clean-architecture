<?php

declare(strict_types=1);

namespace Clean\Application\UseCase;

use Clean\Application\Exception\ArticleNotFound;
use Clean\Application\Port\Ingoing\AddComment;
use Clean\Application\Port\Outgoing\ArticleRepository;
use Clean\Application\Port\Outgoing\CommentRepository;
use Clean\Domain\Entity\Comment;

final class AddCommentUseCase implements AddComment
{
    public function __construct(
        private CommentRepository $commentRepository,
        private ArticleRepository $articleRepository,
    )
    {
    }

    public function add(string $articleSlug, string $body, int $authorId): int
    {
        if (!$this->articleRepository->existsBySlug($articleSlug)) {
            throw new ArticleNotFound();
        }

        $comment = new Comment($articleSlug, $body, $authorId);
        $commentId = $this->commentRepository->save($comment);

        return $commentId;
    }
}
