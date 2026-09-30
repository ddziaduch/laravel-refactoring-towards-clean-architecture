<?php

namespace Clean\Adapter\Secondary\Eloquent;

use App\Models\Comment as CommentModel;
use Clean\Application\Port\Secondary\CommentRepository;
use Clean\Domain\Entity\Comment;
use ReflectionProperty;

final class EloquentCommentRepository implements CommentRepository
{
    public function save(Comment $comment): void
    {
        $model = CommentModel::query()->create([
            'article_id' => $comment->articleId,
            'body' => $comment->body,
            'user_id' => $comment->authorId,
        ]);

        // The database generates the ID, so it has to be written back into the entity.
        (new ReflectionProperty(Comment::class, 'id'))->setValue($comment, $model->id);
    }
}
