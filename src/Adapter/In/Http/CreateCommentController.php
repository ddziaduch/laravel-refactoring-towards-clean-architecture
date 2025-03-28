<?php

declare(strict_types=1);

namespace Clean\Adapter\In\Http;

use App\Http\Requests\Comment\StoreRequest;
use App\Http\Resources\CommentResource;
use App\Models\Article;
use Clean\Application\Port\In\CreateCommentUserCaseInterface;
use Clean\Application\Port\Out\CommentReadModelFinder;
use Clean\Domain\Exception\ArticleNotFound;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

final class CreateCommentController
{
    private CreateCommentUserCaseInterface $useCase;
    private CommentReadModelFinder $commentReadModelFinder;

    public function __construct(
        CreateCommentUserCaseInterface $useCase,
        CommentReadModelFinder $commentReadModelFinder
    ) {
        $this->useCase = $useCase;
        $this->commentReadModelFinder = $commentReadModelFinder;
    }

    public function __invoke(string $article, StoreRequest $request)
    {
        try {
            $id = ($this->useCase)(
                $article,
                Auth::id(),
                $request->get('comment')['body'],
            );
        } catch (ArticleNotFound $e) {
            return new JsonResponse('Article not found', 404);
        }

        $commentReadModel = $this->commentReadModelFinder->get($id);

        return new CommentResource($commentReadModel);
    }
}
