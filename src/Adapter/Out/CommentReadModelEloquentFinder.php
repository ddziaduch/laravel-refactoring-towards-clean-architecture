<?php

declare(strict_types=1);

namespace Clean\Adapter\Out;

use App\Models\Comment;
use Clean\Application\Port\Out\CommentReadModelFinder;
use Clean\Application\ReadModel\CommentReadModel;
use Clean\Application\ReadModel\UserReadModel;

final class CommentReadModelEloquentFinder implements CommentReadModelFinder
{
    public function get(int $commentId): CommentReadModel
    {
        $eloquentComment = Comment::find($commentId);

        return new CommentReadModel(
            $eloquentComment->id,
            $eloquentComment->body,
            $eloquentComment->created_at,
            $eloquentComment->updated_at,
            new UserReadModel(
                $eloquentComment->user->username,
                $eloquentComment->user->bio,
                $eloquentComment->user->image,
                $eloquentComment->user->followers->flatten('id')->toArray(),
            )
        );
    }
}
