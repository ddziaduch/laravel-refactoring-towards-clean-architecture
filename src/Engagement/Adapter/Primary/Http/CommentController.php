<?php

namespace Conduit\Engagement\Adapter\Primary\Http;

use App\Exceptions\ConduitException;
use Closure;
use Conduit\Engagement\Application\Exception\ArticleNotFound;
use Conduit\Engagement\Application\Exception\CommentNotFound;
use Conduit\Engagement\Application\Port\Primary\AddComment;
use Conduit\Engagement\Application\Port\Primary\DeleteComment;
use Conduit\Engagement\Application\Port\Primary\GetComment;
use Conduit\Engagement\Application\Port\Primary\ListComments;
use Conduit\Engagement\Application\ReadModel\CommentReadModel;
use Conduit\Engagement\Domain\Exception\CommentBodyCannotBeEmpty;
use Conduit\Engagement\Domain\Exception\NotCommentAuthor;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\Response as HttpStatus;

final readonly class CommentController
{
    public function index(string $slug, Request $request, ListComments $listComments): JsonResponse
    {
        return $this->translateErrors(fn (): JsonResponse => response()->json([
            'comments' => array_map(
                fn (CommentReadModel $comment): array => (new CommentResource($comment))->toArray($request),
                $listComments($slug, $this->viewerId($request)),
            ),
        ]));
    }

    public function store(string $slug, AddCommentRequest $request, AddComment $addComment, GetComment $getComment): JsonResponse
    {
        return $this->translateErrors(function () use ($slug, $request, $addComment, $getComment): JsonResponse {
            $commentId = $addComment($slug, $request->body(), $this->viewerId($request));

            return (new CommentResource($getComment($commentId, $this->viewerId($request))))
                ->response()
                ->setStatusCode(HttpStatus::HTTP_CREATED);
        });
    }

    public function destroy(string $slug, int $commentId, Request $request, DeleteComment $deleteComment): Response
    {
        return $this->translateErrors(function () use ($slug, $commentId, $request, $deleteComment): Response {
            $deleteComment($slug, $commentId, $this->viewerId($request));

            return response()->noContent();
        });
    }

    /**
     * @template T
     *
     * @param  Closure(): T  $action
     * @return T
     */
    private function translateErrors(Closure $action): mixed
    {
        try {
            return $action();
        } catch (ArticleNotFound) {
            throw new ConduitException(['article' => ['not found']], HttpStatus::HTTP_NOT_FOUND);
        } catch (CommentNotFound) {
            throw new ConduitException(['comment' => ['not found']], HttpStatus::HTTP_NOT_FOUND);
        } catch (NotCommentAuthor) {
            throw new ConduitException(['comment' => ['forbidden']], HttpStatus::HTTP_FORBIDDEN);
        } catch (CommentBodyCannotBeEmpty) {
            throw new ConduitException(['body' => ["can't be blank"]], HttpStatus::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    private function viewerId(Request $request): ?int
    {
        return $request->user()?->getAuthIdentifier();
    }
}
