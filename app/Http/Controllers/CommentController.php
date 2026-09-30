<?php

namespace App\Http\Controllers;

use App\Http\Requests\Comment\DestroyRequest;
use App\Http\Requests\Comment\StoreRequest;
use App\Http\Resources\CommentCollection;
use App\Http\Resources\CommentResource;
use App\Models\Article;
use App\Models\Comment;
use Clean\Application\UseCase\CreateCommentUseCase;
use Illuminate\Http\Response;

class CommentController extends Controller
{
    protected Comment $comment;

    public function __construct(Comment $comment)
    {
        $this->comment = $comment;
    }

    public function index(Article $article)
    {
        return new CommentCollection(
            $article->comments()->with('user.followers')->get(),
        );
    }

    public function store(Article $article, StoreRequest $request, CreateCommentUseCase $createComment)
    {
        $commentId = $createComment($article->id, $request->comment['body'], auth()->id());

        return (new CommentResource($this->comment->findOrFail($commentId)->load('user.followers')))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function destroy(Article $article, Comment $comment, DestroyRequest $request): Response
    {
        $comment->delete();

        return response()->noContent();
    }
}
