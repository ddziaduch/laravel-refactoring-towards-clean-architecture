<?php

declare(strict_types=1);

namespace Clean\Application\UseCase;

use App\Models\Article;
use Clean\Application\Port\Outgoing\CommentRepository;

final class AddCommentUseCase
{
    public function __construct(
        private CommentRepository $commentRepository,
    )
    {
    }

    public function add(string $articleSlug, string $body, int $authorId): int
    {
        $commentId = $this->commentRepository->save(
            $articleSlug,
            $body,
            $authorId
        );

        return $commentId;
    }
}
