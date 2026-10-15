<?php

declare(strict_types=1);

namespace Clean\Application\UseCase;

use App\Models\Article;
use App\Models\Comment;

final class AddCommentUseCase
{
    public function add(Article $article, string $body, int $userId): Comment
    {
        return $article->comments()->create([
            'body' => $body,
            'user_id' => $userId
        ]);
    }
}
