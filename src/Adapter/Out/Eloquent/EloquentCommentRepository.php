<?php

namespace Clean\Adapter\Out\Eloquent;

use App\Models\Article;
use Clean\Application\Port\Out\CommentRepository;
use Clean\Domain\Entity\Comment;

class EloquentCommentRepository implements CommentRepository
{
    public function save(Comment $comment): void
    {
        $id = Article::where('slug', $comment->articleSlug)
            ->get()
            ->first()
            ->comments()
            ->create(['body' => $comment->body(), 'user_id' => $comment->authorId])
            ->id;

        $reflectionObject = new \ReflectionObject($comment);
        $reflectionProperty = $reflectionObject->getProperty('id');
        $reflectionProperty->setAccessible(true);
        $reflectionProperty->setValue($comment, $id);
    }
}
