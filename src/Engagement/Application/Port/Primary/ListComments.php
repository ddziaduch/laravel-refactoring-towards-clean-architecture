<?php

namespace Conduit\Engagement\Application\Port\Primary;

use Conduit\Engagement\Application\Exception\ArticleNotFound;
use Conduit\Engagement\Application\ReadModel\CommentReadModel;

interface ListComments
{
    /**
     * @return list<CommentReadModel>
     *
     * @throws ArticleNotFound
     */
    public function __invoke(string $articleSlug, ?int $viewerId): array;
}
