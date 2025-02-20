<?php

declare(strict_types=1);

namespace Clean\Adapter\In\Http;

use App\Http\Requests\Article\StoreRequest;
use Clean\Application\Port\In\CreateArticleUseCasePort;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\JsonResponse;

final class CreateArticleHttpController
{
    private CreateArticleUseCasePort $useCase;
    private Guard $guard;

    public function __construct(
        CreateArticleUseCasePort $useCase,
        Guard $guard
    ) {
        $this->guard = $guard;
        $this->useCase = $useCase;
    }

    public function __invoke(StoreRequest $request): JsonResponse
    {
        $input = $request->validated()['article'];
        $articleReadModel = ($this->useCase)(
            $this->guard->id(),
            $input['title'],
            $input['description'],
            $input['body'],
            ...($input['tagList'] ?? []),
        );

        return (new ArticleReadModelResource($articleReadModel, $this->guard))
            ->toResponse($request)
            ->setStatusCode(201);
    }
}
