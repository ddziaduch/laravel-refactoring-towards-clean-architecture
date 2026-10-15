<?php

declare(strict_types=1);

namespace Clean\Adapter\Outgoing;

use App\Models\Article;
use Clean\Application\Port\Outgoing\CommentRepository;

final class LaravelCommentRepository implements CommentRepository
{
    public function save(string $slug, string $body, int $authorId): int
    {
        $comment = Article::query()
            ->where(['slug' => $slug])
            ->firstOrFail()
            ->comments()
            ->create([
                'body' => $body,
                'user_id' => $authorId,
            ]);

        return $comment->id;
    }
}
