<?php

declare(strict_types=1);

namespace Clean\Application\UseCase;

use App\Models\Article;
use App\Models\User;
use App\Services\ArticleService;
use Clean\Application\Port\In\CreateArticleUseCasePort;

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
    ): Article {
        $article = User::where('id', $authorId)->firstOrFail()->articles()->create([
            'title' => $title,
            'description' => $description,
            'body' => $body,
        ]);

        $this->articleService->syncTags($article, $tagList);

        return $article;
    }
}
