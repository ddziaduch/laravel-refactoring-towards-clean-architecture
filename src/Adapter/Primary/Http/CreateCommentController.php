<?php

namespace Clean\Adapter\Primary\Http;

use App\Exceptions\ConduitException;
use Clean\Application\Exception\ArticleNotFound;
use Clean\Application\Port\Primary\CreateComment;
use Clean\Application\Port\Secondary\GetCommentReadModel;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

final readonly class CreateCommentController
{
    public function __construct(
        private CreateComment $createComment,
        private GetCommentReadModel $getCommentReadModel,
    ) {}

    public function __invoke(string $article, CreateCommentRequest $request): JsonResponse
    {
        $userId = $request->user()->id;

        try {
            $commentId = ($this->createComment)($article, $request->body(), $userId);
        } catch (ArticleNotFound) {
            throw new ConduitException(['article' => ['not found']], Response::HTTP_NOT_FOUND);
        }

        $comment = $this->getCommentReadModel->get($commentId, $userId);

        return (new CommentReadModelResource($comment))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }
}
