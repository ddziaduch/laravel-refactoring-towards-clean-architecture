<?php

declare(strict_types=1);

namespace Clean\Adapter\In\Http;

use App\Http\Requests\Article\StoreRequest;
use App\Http\Resources\ArticleResource;
use App\Models\Article;
use App\Services\ArticleService;

final class CreateArticleHttpController
{

    public function __construct(
        private ArticleService $articleService,
    ) {
    }
    public function __invoke(StoreRequest $request): ArticleResource
    {
        $article = auth()->user()->articles()->create($request->validated()['article']);

        $this->articleService->syncTags($article, $request->validated()['article']['tagList'] ?? []);

        return $this->articleResponse($article);
    }

    private function articleResponse(Article $article): ArticleResource
    {
        return new ArticleResource($article->load('user', 'users', 'tags', 'user.followers'));
    }
}
