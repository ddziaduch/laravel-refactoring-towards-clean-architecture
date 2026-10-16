<?php

namespace Conduit\Engagement\Application\UseCase;

use Conduit\Engagement\Application\Exception\ArticleNotFound;
use Conduit\Engagement\Application\Exception\CommentNotFound;
use Conduit\Engagement\Application\Port\Primary\DeleteComment;
use Conduit\Engagement\Application\Port\Secondary\ArticleLookup;
use Conduit\Engagement\Application\Port\Secondary\CommentRepository;

final readonly class DeleteCommentUseCase implements DeleteComment
{
    public function __construct(
        private ArticleLookup $articles,
        private CommentRepository $comments,
    ) {}

    public function __invoke(string $articleSlug, int $commentId, int $userId): void
    {
        $articleId = $this->articles->idOfSlug($articleSlug) ?? throw ArticleNotFound::withSlug($articleSlug);
        $comment = $this->comments->find($commentId);

        if ($comment === null || ! $comment->belongsTo($articleId)) {
            throw CommentNotFound::withId($commentId);
        }

        $comment->assertAuthoredBy($userId);

        $this->comments->remove($comment);
    }
}
