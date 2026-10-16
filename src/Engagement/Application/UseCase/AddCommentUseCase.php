<?php

namespace Conduit\Engagement\Application\UseCase;

use Conduit\Engagement\Application\Exception\ArticleNotFound;
use Conduit\Engagement\Application\Port\Primary\AddComment;
use Conduit\Engagement\Application\Port\Secondary\ArticleLookup;
use Conduit\Engagement\Application\Port\Secondary\CommentRepository;
use Conduit\Engagement\Domain\Comment;

final readonly class AddCommentUseCase implements AddComment
{
    public function __construct(
        private ArticleLookup $articles,
        private CommentRepository $comments,
    ) {}

    public function __invoke(string $articleSlug, string $body, int $authorId): int
    {
        $articleId = $this->articles->idOfSlug($articleSlug) ?? throw ArticleNotFound::withSlug($articleSlug);

        return $this->comments->add(Comment::write($articleId, $authorId, $body));
    }
}
