<?php

namespace Clean\Adapter\In\Http;

use App\Http\Controllers\Controller;
use Clean\Adapter\In\Http\CreateCommentRequest;
use Clean\Application\Port\UseCase\CreateCommentUseCasePort;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\JsonResponse;

class CreateCommentController extends Controller
{
    public function __construct(
        private readonly Guard $guard,
        private readonly CreateCommentUseCasePort $createCommentUseCasePort,
    ) {
    }

    public function __invoke(CreateCommentRequest $request): JsonResponse
    {
        $commentReadModel = $this->createCommentUseCasePort->create(
            $request->route('article'),
            $request->comment['body'],
            $this->guard->id(),
        );

        return new JsonResponse(
            new CommentReadModelResource($commentReadModel),
            201,
        );
    }
}
