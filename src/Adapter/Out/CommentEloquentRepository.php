<?php

declare(strict_types=1);

namespace Clean\Adapter\Out;

use App\Models\Article;
use Clean\Domain\Entity\Comment;
use Clean\Domain\Port\Out\CommentRepository;
use ReflectionClass;

final class CommentEloquentRepository implements CommentRepository
{
    public function createComment(Comment $comment): int
    {
        $eloquentComment = Article::where('slug', $comment->getArticleSlug())
            ->firstOrFail()
            ->comments()
            ->create([
                'user_id' => $comment->getAuthorId(),
                'body' => $comment->getBody(),
            ]);

        return $eloquentComment->id;
    }
}
