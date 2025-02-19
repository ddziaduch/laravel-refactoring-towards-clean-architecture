<?php

namespace Clean\Adapter\Out\Eloquent;

use App\Models\Comment;
use Clean\Application\Port\Out\GetCommentReadModel;
use Clean\Application\ReadModel\CommentReadModel;
use Clean\Application\ReadModel\ProfileReadModel;

class EloquentGetCommentReadModel implements GetCommentReadModel
{
    public function get(int $id, ?int $currentUserId = null): CommentReadModel
    {
         $activeRecord = Comment::where('id', $id)->get()->first();

         return new CommentReadModel(
             $activeRecord->id,
             $activeRecord->created_at,
             $activeRecord->updated_at,
             $activeRecord->body,
             new ProfileReadModel(
                 $activeRecord->user->username,
                 $activeRecord->user->bio,
                 $activeRecord->user->image,
                 $currentUserId ? $activeRecord->user->followers->contains($currentUserId) : false,
             ),
         );
    }
}
