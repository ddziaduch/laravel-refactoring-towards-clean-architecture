<?php

namespace App\Http\Controllers;

use App\Http\Requests\Comment\DestroyRequest;
use App\Http\Requests\Comment\StoreRequest;
use App\Http\Resources\CommentCollection;
use App\Http\Resources\CommentResource;
use App\Models\Article;
use App\Models\Comment;
use Clean\Application\Port\UseCase\CreateCommentUseCasePort;
use Illuminate\Http\JsonResponse;

class CommentController extends Controller
{
    protected Comment $comment;

    public function __construct(
        Comment $comment,
        private readonly CreateCommentUseCasePort $createCommentUseCasePort,
    ) {
        $this->comment = $comment;
    }

    public function index(Article $article)
    {
        return new CommentCollection($article->comments);
    }

    public function store(Article $article, StoreRequest $request)
    {
        // create in the new way
        $id = $this->createCommentUseCasePort->create(
            $article->id,
            $request->comment['body'],
            auth()->id(),
        );
        // retrieve in the old way
        $comment = $article->comments()->find($id);

        return new JsonResponse(new CommentResource($comment), 201);
    }

    public function destroy(Article $article, Comment $comment, DestroyRequest $request): void
    {
        $comment->delete();
    }
}
