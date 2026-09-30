<?php

namespace Clean\Adapter\Secondary\Eloquent;

use App\Models\Comment;
use Clean\Application\Port\Secondary\CommentRepository;

final class EloquentCommentRepository implements CommentRepository
{
    public function save(int $articleId, string $body, int $authorId): int
    {
        return Comment::query()->create([
            'article_id' => $articleId,
            'body' => $body,
            'user_id' => $authorId,
        ])->id;
    }
}
