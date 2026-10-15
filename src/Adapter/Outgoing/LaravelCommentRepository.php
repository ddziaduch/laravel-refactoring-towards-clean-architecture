<?php

declare(strict_types=1);

namespace Clean\Adapter\Outgoing;

use App\Models\Article;
use Clean\Application\Port\Outgoing\CommentRepository;
use Clean\Domain\Entity\Comment;

final class LaravelCommentRepository implements CommentRepository
{
    public function save(Comment $comment): int
    {
        $comment = Article::query()
            ->where(['slug' => $comment->articleSlug])
            ->first()
            ->comments()
            ->create([
                'body' => $comment->body,
                'user_id' => $comment->authorId,
            ]);

        return $comment->id;
    }
}
