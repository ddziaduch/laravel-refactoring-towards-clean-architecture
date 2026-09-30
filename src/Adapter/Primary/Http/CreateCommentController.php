<?php

namespace Clean\Adapter\Primary\Http;

use App\Exceptions\ConduitException;
use App\Http\Resources\CommentResource;
use App\Models\Comment;
use Clean\Application\Exception\ArticleNotFound;
use Clean\Application\Port\Primary\CreateComment;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

final readonly class CreateCommentController
{
    public function __construct(
        private CreateComment $createComment,
    ) {}

    public function __invoke(string $article, CreateCommentRequest $request): JsonResponse
    {
        try {
            $commentId = ($this->createComment)($article, $request->body(), $request->user()->id);
        } catch (ArticleNotFound) {
            throw new ConduitException(['article' => ['not found']], Response::HTTP_NOT_FOUND);
        }

        return (new CommentResource(Comment::query()->findOrFail($commentId)->load('user.followers')))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }
}
