<?php

namespace Clean\Application\Port\Secondary;

use Clean\Application\ReadModel\CommentReadModel;

interface GetCommentReadModel
{
    public function get(int $commentId, ?int $viewerId): CommentReadModel;
}
