<?php

declare(strict_types=1);

namespace Clean\Adapter\In\Http;

use App\Http\Requests\Article\StoreRequest;
use App\Http\Resources\ArticleResource;
use App\Models\Article;
use Clean\Application\Port\In\CreateArticleUseCasePort;
use Illuminate\Contracts\Auth\Guard;

final class CreateArticleHttpController
{
    public function __construct(
        private readonly CreateArticleUseCasePort $useCase,
        private readonly Guard $guard,
    )
    {
    }

    public function __invoke(StoreRequest $request): ArticleResource
    {
        $input = $request->validated()['article'];
        $article = ($this->useCase)(
            $this->guard->id(),
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
