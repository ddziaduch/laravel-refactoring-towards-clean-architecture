<?php

namespace Clean\Adapter\Ingoing\Http;

use App\Http\Controllers\Controller;
use App\Http\Requests\Comment\DestroyRequest;
use App\Http\Requests\Comment\StoreRequest;
use App\Http\Resources\CommentCollection;
use App\Http\Resources\CommentResource;
use App\Models\Article;
use App\Models\Comment;
use Clean\Application\Exception\ArticleNotFound;
use Clean\Application\UseCase\AddCommentUseCase;
use Illuminate\Http\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AddCommentController extends Controller
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

    public function store(string $articleSlug, StoreRequest $request, AddCommentUseCase $useCase)
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

    public function destroy(Article $article, Comment $comment, DestroyRequest $request): Response
    {
        $comment->delete();

        return response()->noContent();
    }
}
