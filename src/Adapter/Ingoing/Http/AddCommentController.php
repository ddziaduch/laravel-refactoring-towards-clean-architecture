<?php

namespace Clean\Adapter\Ingoing\Http;

use App\Http\Requests\Comment\StoreRequest;
use App\Http\Resources\CommentResource;
use App\Models\Comment;
use Clean\Application\Exception\ArticleNotFound;
use Clean\Application\Port\Ingoing\AddComment;
use Illuminate\Http\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AddCommentController
{
    public function store(string $articleSlug, StoreRequest $request, AddComment $useCase)
    {
        try {
            $commentId = $useCase->add(
                $articleSlug,
                $request->comment['body'],
                auth()->id(),
            );
        } catch (ArticleNotFound) {
            throw new NotFoundHttpException();
        }

        return (new CommentResource(
            Comment::query()
                ->findOrFail($commentId)
                ->load('user.followers'),
        ))->response()->setStatusCode(Response::HTTP_CREATED);
    }
}
