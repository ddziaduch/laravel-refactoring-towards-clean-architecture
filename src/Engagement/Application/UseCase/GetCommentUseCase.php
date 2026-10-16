<?php

namespace Conduit\Engagement\Application\UseCase;

use Conduit\Engagement\Application\Exception\CommentNotFound;
use Conduit\Engagement\Application\Port\Primary\GetComment;
use Conduit\Engagement\Application\Port\Secondary\CommentQueries;
use Conduit\Engagement\Application\ReadModel\CommentReadModel;
use Conduit\Engagement\Application\Service\CommentAssembler;

final readonly class GetCommentUseCase implements GetComment
{
    public function __construct(
        private CommentQueries $comments,
        private CommentAssembler $assembler,
    ) {}

    public function __invoke(int $commentId, ?int $viewerId): CommentReadModel
    {
        $record = $this->comments->byId($commentId) ?? throw CommentNotFound::withId($commentId);

        return $this->assembler->assemble([$record], $viewerId)[0];
    }
}
