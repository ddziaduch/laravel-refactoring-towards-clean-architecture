<?php

namespace Clean\Adapter\Primary\Http;

use App\Exceptions\ConduitException;
use Clean\Application\Exception\ArticleNotFound;
use Clean\Application\Exception\CommentNotFound;
use Clean\Application\Port\Primary\DeleteComment;
use Clean\Domain\Exception\OnlyAuthorCanDeleteComment;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final readonly class DeleteCommentController
{
    public function __construct(
        private DeleteComment $deleteComment,
    ) {}

    public function __invoke(string $article, string $comment, Request $request): Response
    {
        try {
            ($this->deleteComment)($article, (int) $comment, $request->user()->id);
        } catch (ArticleNotFound) {
            throw new ConduitException(['article' => ['not found']], Response::HTTP_NOT_FOUND);
        } catch (CommentNotFound) {
            throw new ConduitException(['comment' => ['not found']], Response::HTTP_NOT_FOUND);
        } catch (OnlyAuthorCanDeleteComment) {
            throw new ConduitException(['comment' => ['forbidden']], Response::HTTP_FORBIDDEN);
        }

        return response()->noContent();
    }
}
