<?php

namespace Clean\Adapter\Secondary\Eloquent;

use App\Models\Comment as CommentModel;
use Clean\Application\Port\Secondary\CommentRepository;
use Clean\Domain\Entity\Comment;
use DateTimeImmutable;

final class EloquentCommentRepository implements CommentRepository
{
    public function find(int $commentId): ?Comment
    {
        $model = CommentModel::query()->find($commentId);

        if ($model === null) {
            return null;
        }

        return new Comment(
            $model->article_id,
            $model->user_id,
            $model->body,
            $model->id,
            $model->deleted_at === null ? null : new DateTimeImmutable($model->deleted_at),
        );
    }

    public function save(Comment $comment): int
    {
        if ($comment->id === null) {
            return CommentModel::query()->create([
                'article_id' => $comment->articleId,
                'body' => $comment->body,
                'user_id' => $comment->authorId,
            ])->id;
        }

        CommentModel::query()->whereKey($comment->id)->update([
            'body' => $comment->body,
            'deleted_at' => $comment->deletedAt(),
        ]);

        return $comment->id;
    }
}
