<?php

declare(strict_types=1);

namespace Clean\Adapter\Out;

use App\Models\Article;
use App\Models\User;
use Clean\Application\Port\Out\ArticleReadModelGetter;
use Clean\Application\ReadModel\ArticleReadModel;
use Clean\Application\ReadModel\ProfileReadModel;

final class EloquentArticleReadModelGetter implements ArticleReadModelGetter
{
    public function get(int $id, ?int $currentUserId): ArticleReadModel
    {
        $currentUser = User::where('id', $currentUserId)->firstOrFail();
        assert($currentUser instanceof User);
        $eloquentArticle = Article::where('id', $id)->firstOrFail()->load('user', 'users', 'tags', 'user.followers');
        assert($eloquentArticle instanceof Article);
        $author = $eloquentArticle->user;
        assert($author instanceof User);

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
            $currentUser->favoritedArticles->contains($eloquentArticle->id),
            new ProfileReadModel(
                $author->id,
                $author->username,
                $author->bio,
                $author->image,
                $currentUser->following->contains($eloquentArticle->id),
            ),
        );
    }
}
