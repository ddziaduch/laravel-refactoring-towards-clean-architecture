<?php

namespace Conduit\Engagement\Application\UseCase;

use Conduit\Engagement\Application\Exception\ArticleNotFound;
use Conduit\Engagement\Application\Port\Primary\ListComments;
use Conduit\Engagement\Application\Port\Secondary\ArticleLookup;
use Conduit\Engagement\Application\Port\Secondary\CommentQueries;
use Conduit\Engagement\Application\Service\CommentAssembler;

final readonly class ListCommentsUseCase implements ListComments
{
    public function __construct(
        private ArticleLookup $articles,
        private CommentQueries $comments,
        private CommentAssembler $assembler,
    ) {}

    public function __invoke(string $articleSlug, ?int $viewerId): array
    {
        $articleId = $this->articles->idOfSlug($articleSlug) ?? throw ArticleNotFound::withSlug($articleSlug);

        return $this->assembler->assemble($this->comments->ofArticle($articleId), $viewerId);
    }
}
