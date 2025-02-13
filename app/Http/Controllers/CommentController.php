<?php

namespace App\Http\Controllers;

use App\Http\Requests\Comment\DestroyRequest;
use App\Http\Resources\CommentCollection;
use App\Models\Article;
use App\Models\Comment;

class CommentController extends Controller
{
    protected Comment $comment;

    public function __construct(
        Comment $comment,
    ) {
        $this->comment = $comment;
    }

    public function index(Article $article)
    {
        return new CommentCollection($article->comments);
    }

    public function destroy(Article $article, Comment $comment, DestroyRequest $request): void
    {
        $comment->delete();
    }
}
