<?php

namespace Clean\Adapter\Secondary\Eloquent;

use App\Models\Comment as CommentModel;
use Clean\Application\Port\Secondary\CommentRepository;
use Clean\Domain\Entity\Comment;

final class EloquentCommentRepository implements CommentRepository
{
    public function save(Comment $comment): int
    {
        return CommentModel::query()->create([
            'article_id' => $comment->articleId,
            'body' => $comment->body,
            'user_id' => $comment->authorId,
        ])->id;
    }
}
