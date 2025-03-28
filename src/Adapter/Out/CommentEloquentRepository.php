<?php

declare(strict_types=1);

namespace Clean\Adapter\Out;

use App\Models\Article;
use Clean\Domain\Entity\Comment;
use Clean\Domain\Exception\ArticleNotFound;
use Clean\Domain\Port\Out\CommentRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;

final class CommentEloquentRepository implements CommentRepository
{
    public function createComment(Comment $comment): int
    {
        try {
            $eloquentComment = Article::where('slug', $comment->getArticleSlug())
                ->firstOrFail()
                ->comments()
                ->create([
                    'user_id' => $comment->getAuthorId(),
                    'body' => $comment->getBody(),
                ]);
        } catch (ModelNotFoundException $e) {
            throw new ArticleNotFound($e->getMessage(), $e->getCode(), $e);
        }

        return $eloquentComment->id;
    }
}
