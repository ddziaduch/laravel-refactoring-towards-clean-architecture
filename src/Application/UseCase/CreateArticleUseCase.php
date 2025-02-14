<?php

declare(strict_types=1);

namespace Clean\Application\UseCase;

use App\Models\Article;
use App\Models\User;
use App\Services\ArticleService;
use Clean\Application\Port\In\CreateArticleUseCasePort;
use Clean\Application\ReadModel\ArticleReadModel;
use Clean\Application\ReadModel\ProfileReadModel;

final class CreateArticleUseCase implements CreateArticleUseCasePort
{
    public function __construct(
        private readonly ArticleService $articleService,
    ) {
    }

    public function __invoke(
        int $authorId,
        string $title,
        string $description,
        string $body,
        string ...$tagList,
    ): ArticleReadModel {
        // create an entity here
        // move below to the repository
        $user = User::where('id', $authorId)->firstOrFail();
        assert($user instanceof User);

        $article = $user->articles()->create([
            'title' => $title,
            'description' => $description,
            'body' => $body,
        ]);
        assert($article instanceof Article);

        $this->articleService->syncTags($article, $tagList);

        // return a read model
        return new ArticleReadModel(
            $article->slug,
            $article->title,
            $article->description,
            $article->body,
            $article->tags->pluck('name')->all(),
            $article->created_at,
            $article->updated_at,
            (int) $article->users_count,
            $article->users->contains($user->id),
            new ProfileReadModel(
                $user->username,
                $user->bio,
                $user->image,
                $user->followers->contains($user->id),
            ),
        );
    }
}
