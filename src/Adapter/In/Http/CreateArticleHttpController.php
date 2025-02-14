<?php

declare(strict_types=1);

namespace Clean\Adapter\In\Http;

use App\Http\Requests\Article\StoreRequest;
use Clean\Application\Port\In\CreateArticleUseCasePort;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\JsonResponse;

final class CreateArticleHttpController
{
    public function __construct(
        private readonly CreateArticleUseCasePort $useCase,
        private readonly Guard $guard,
    )
    {
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

        return (new CreatedArticleReadModelResource($articleReadModel))
            ->toResponse($request)
            ->setStatusCode(201);
    }
}
