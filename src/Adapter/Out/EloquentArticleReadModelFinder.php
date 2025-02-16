<?php

declare(strict_types=1);

namespace Clean\Adapter\Out;

use App\Models\Article;
use Clean\Application\Port\Out\ArticleReadModelFinder;
use Clean\Application\ReadModel\ArticleReadModel;
use Clean\Application\ReadModel\ProfileReadModel;

final class EloquentArticleReadModelFinder implements ArticleReadModelFinder
{
    public function bySlug(string $slug): ArticleReadModel
    {
        $eloquentArticle = Article::where('slug', $slug)->firstOrFail()->load('user', 'users', 'tags', 'user.followers');
        $user = $eloquentArticle->user;

        return new ArticleReadModel(
            $eloquentArticle->id,
            $eloquentArticle->slug,
            $eloquentArticle->title,
            $eloquentArticle->description,
            $eloquentArticle->body,
            $eloquentArticle->tags->pluck('name')->all(),
            $eloquentArticle->created_at,
            $eloquentArticle->updated_at,
            (int) $eloquentArticle->users_count,
            new ProfileReadModel(
                $user->id,
                $user->username,
                $user->bio,
                $user->image,
            ),
        );
    }
}
