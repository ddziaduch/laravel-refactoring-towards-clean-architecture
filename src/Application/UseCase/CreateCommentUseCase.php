<?php

namespace Clean\Application\UseCase;

use App\Models\Article;
use Clean\Application\Port\UseCase\CreateCommentUseCasePort;

final class CreateCommentUseCase implements CreateCommentUseCasePort
{
    public function create(int $articleId, string $commentBody, int $authorId): int
    {
        return Article::where('id', $articleId)
            ->get()
            ->first()
            ->comments()
            ->create(['body' => $commentBody, 'user_id' => $authorId])
            ->id;
    }
}
