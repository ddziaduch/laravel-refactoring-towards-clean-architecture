<?php

declare(strict_types = 1);

namespace Clean\Adapter\In\Http;

use Clean\Application\Exception\ArticleDoesNotExist;
use Clean\Application\Port\In\DeleteArticleUseCasePort;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class DeleteArticleHttpController
{
    private DeleteArticleUseCasePort $useCase;
    private Guard $guard;

    public function __construct(
        DeleteArticleUseCasePort $useCase,
        Guard $guard
    ) {
        $this->guard = $guard;
        $this->useCase = $useCase;
    }

    public function __invoke(Request $request): Response
    {
        try {
            $this->useCase->handle(
                $this->guard->id(),
                (string) $request->route('article'),
            );
        } catch (ArticleDoesNotExist $e) {
            return new JsonResponse($e->getMessage(), Response::HTTP_NOT_FOUND);
        }

        return new JsonResponse();
    }
}
