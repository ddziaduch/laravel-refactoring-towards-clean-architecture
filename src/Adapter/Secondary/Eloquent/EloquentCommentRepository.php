<?php

namespace Clean\Adapter\Secondary\Eloquent;

use App\Models\Comment as CommentModel;
use Clean\Application\Port\Secondary\CommentRepository;
use Clean\Domain\Entity\Comment;

final class EloquentCommentRepository implements CommentRepository
{
    public function find(int $commentId): ?Comment
    {
        $model = CommentModel::query()->find($commentId);

        return $model === null ? null : new Comment($model->article_id, $model->user_id, $model->body);
    }

    public function save(Comment $comment): int
    {
        return CommentModel::query()->create([
            'article_id' => $comment->articleId,
            'body' => $comment->body,
            'user_id' => $comment->authorId,
        ])->id;
    }

    public function delete(int $commentId): void
    {
        CommentModel::query()->whereKey($commentId)->delete();
    }
}
