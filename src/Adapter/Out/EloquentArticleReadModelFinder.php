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
            $eloquentArticle->slug,
            $eloquentArticle->title,
            $eloquentArticle->description,
            $eloquentArticle->body,
            $eloquentArticle->tags->pluck('name')->all(),
            $eloquentArticle->created_at,
            $eloquentArticle->updated_at,
            (int) $eloquentArticle->users_count,
            // inject here current user
            $eloquentArticle->users->contains($user->id),
            new ProfileReadModel(
                $user->username,
                $user->bio,
                $user->image,
                // inject here current user
                $user->followers->contains($user->id),
            ),
        );
    }
}
