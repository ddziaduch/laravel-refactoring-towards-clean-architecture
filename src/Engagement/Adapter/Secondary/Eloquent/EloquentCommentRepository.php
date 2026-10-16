<?php

namespace Conduit\Engagement\Adapter\Secondary\Eloquent;

use Conduit\Engagement\Application\Port\Secondary\CommentRepository;
use Conduit\Engagement\Domain\Comment;

final class EloquentCommentRepository implements CommentRepository
{
    public function add(Comment $comment): int
    {
        return CommentModel::query()->create([
            'article_id' => $comment->articleId,
            'user_id' => $comment->authorId,
            'body' => $comment->body,
        ])->getKey();
    }

    public function find(int $id): ?Comment
    {
        $model = CommentModel::query()->find($id);

        return $model === null
            ? null
            : new Comment($model->getKey(), $model->article_id, $model->user_id, $model->body);
    }

    public function remove(Comment $comment): void
    {
        CommentModel::query()->whereKey($comment->id)->delete();
    }
}
