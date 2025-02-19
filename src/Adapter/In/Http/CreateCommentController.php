<?php

namespace Clean\Adapter\In\Http;

use App\Http\Controllers\Controller;
use Clean\Application\Port\Out\GetCommentReadModel;
use Clean\Application\Port\UseCase\CreateCommentUseCasePort;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Http\JsonResponse;

class CreateCommentController extends Controller
{
    private CreateCommentUseCasePort $createCommentUseCasePort;
    private Guard $guard;
    private GetCommentReadModel $getCommentReadModel;

    public function __construct(
        Guard $guard,
        CreateCommentUseCasePort $createCommentUseCasePort,
        GetCommentReadModel $getCommentReadModel
    ) {
        $this->guard = $guard;
        $this->createCommentUseCasePort = $createCommentUseCasePort;
        $this->getCommentReadModel = $getCommentReadModel;
    }

    public function __invoke(CreateCommentRequest $request): JsonResponse
    {
        $id = $this->createCommentUseCasePort->create(
            $request->route('article'),
            $request->comment['body'],
            $this->guard->id(),
        );

        $commentReadModel = $this->getCommentReadModel->get($id, $this->guard->id());

        return new JsonResponse(
            new CommentReadModelResource($commentReadModel),
            201,
        );
    }
}
