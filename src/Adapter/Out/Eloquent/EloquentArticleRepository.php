<?php

declare(strict_types = 1);

namespace Clean\Adapter\Out\Eloquent;

use App\Models\Article;
use Clean\Application\Exception\ArticleDoesNotExist;
use Clean\Application\Port\Out\ArticleRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class EloquentArticleRepository implements ArticleRepository
{
    public function delete(int $authorId, string $articleSlug): void
    {
        try {
            Article::where('slug', $articleSlug)
                ->where('user_id', $authorId)
                ->firstOrFail()
                ->delete();
        } catch (ModelNotFoundException $exception) {
            throw ArticleDoesNotExist::forAuthorIdAndSlug($authorId, $articleSlug);
        }
    }
}
