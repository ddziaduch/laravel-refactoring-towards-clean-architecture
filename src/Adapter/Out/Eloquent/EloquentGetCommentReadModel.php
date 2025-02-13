<?php

namespace Clean\Adapter\Out\Eloquent;

use App\Models\Comment;
use Clean\Application\Port\Out\GetCommentReadModel;
use Clean\Application\ReadModel\CommentReadModel;
use Clean\Application\ReadModel\ProfileReadModel;

class EloquentGetCommentReadModel implements GetCommentReadModel
{
    public function get(int $id): CommentReadModel
    {
         $activeRecord = Comment::where('id', $id)->get()->first();

         return new CommentReadModel(
             id: $activeRecord->id,
             createdAt: $activeRecord->created_at,
             updatedAt: $activeRecord->updated_at,
             body: $activeRecord->body,
             author: new ProfileReadModel(
                 username: $activeRecord->user->username,
                 bio: $activeRecord->user->bio,
                 image: $activeRecord->user->image,
                 following: $activeRecord->user->followers->contains(auth()->id())
             ),
         );
    }
}
