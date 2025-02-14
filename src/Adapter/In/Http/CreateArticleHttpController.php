<?php

declare(strict_types=1);

namespace Clean\Adapter\In\Http;

use App\Http\Requests\Article\StoreRequest;
use App\Http\Resources\ArticleResource;
use App\Models\Article;
use Clean\Application\Port\In\CreateArticleUseCasePort;

final class CreateArticleHttpController
{
    public function __construct(
        private readonly CreateArticleUseCasePort $useCase,
    )
    {
    }

    public function __invoke(StoreRequest $request): ArticleResource
    {
        $input = $request->validated()['article'];
        $article = ($this->useCase)(
            auth()->id(),
            $input['title'],
            $input['description'],
            $input['body'],
            ...($input['tagList'] ?? []),
        );

        return $this->articleResponse($article);
    }

    private function articleResponse(Article $article): ArticleResource
    {
        return new ArticleResource($article->load('user', 'users', 'tags', 'user.followers'));
    }
}
