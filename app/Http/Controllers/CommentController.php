<?php

namespace App\Http\Controllers;

use App\Http\Requests\Comment\DestroyRequest;
use App\Http\Requests\Comment\StoreRequest;
use App\Http\Resources\CommentCollection;
use App\Http\Resources\CommentResource;
use App\Models\Article;
use App\Models\Comment;
use Clean\Application\CreateCommentUseCase;
use Clean\Application\Port\Out\CommentReadModelFinder;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    protected Comment $comment;
    private CreateCommentUseCase $useCase;
    private CommentReadModelFinder $commentReadModelFinder;

    public function __construct(
        Comment $comment,
        CreateCommentUseCase $useCase,
        CommentReadModelFinder $commentReadModelFinder
    ) {
        $this->comment = $comment;
        $this->useCase = $useCase;
        $this->commentReadModelFinder = $commentReadModelFinder;
    }

    public function index(Article $article)
    {
        return new CommentCollection($article->comments);
    }

    public function store(Article $article, StoreRequest $request)
    {
        $id = ($this->useCase)(
            $article->slug,
            Auth::id(),
            $request->get('comment')['body'],
        );

        $commentReadModel = $this->commentReadModelFinder->get($id);

        return new CommentResource($commentReadModel);
    }

    public function destroy(Article $article, Comment $comment, DestroyRequest $request): void
    {
        $comment->delete();
    }
}
