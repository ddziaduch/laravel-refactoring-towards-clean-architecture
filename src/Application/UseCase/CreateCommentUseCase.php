<?php

namespace Clean\Application\UseCase;

use App\Models\Article;
use App\Models\Comment;

final class CreateCommentUseCase
{
    public function __invoke(Article $article, string $body, int $authorId): Comment
    {
        return $article->comments()->create(['body' => $body, 'user_id' => $authorId]);
    }
}
