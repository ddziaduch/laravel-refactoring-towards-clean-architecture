<?php

namespace Clean\Adapter\Secondary\Eloquent;

use App\Models\Comment;
use Clean\Application\Port\Secondary\GetCommentReadModel;
use Clean\Application\ReadModel\CommentReadModel;
use Clean\Application\ReadModel\ProfileReadModel;

final class EloquentGetCommentReadModel implements GetCommentReadModel
{
    public function get(int $commentId, ?int $viewerId): CommentReadModel
    {
        $comment = Comment::query()->with('user')->findOrFail($commentId);
        $author = $comment->user;

        return new CommentReadModel(
            $comment->id,
            $comment->created_at->toDateTimeImmutable(),
            $comment->updated_at->toDateTimeImmutable(),
            $comment->body,
            new ProfileReadModel(
                $author->username,
                $author->bio,
                $author->image,
                $viewerId !== null && $author->followers()->whereKey($viewerId)->exists(),
            ),
        );
    }
}
