<?php

namespace Conduit\Engagement\Application\Port\Primary;

use Conduit\Engagement\Application\Exception\CommentNotFound;
use Conduit\Engagement\Application\ReadModel\CommentReadModel;

interface GetComment
{
    /**
     * @throws CommentNotFound
     */
    public function __invoke(int $commentId, ?int $viewerId): CommentReadModel;
}
