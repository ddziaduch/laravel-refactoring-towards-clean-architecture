<?php

namespace Clean\Application\Port\Out;

use Clean\Application\ReadModel\CommentReadModel;

interface GetCommentReadModel
{
    public function get(int $id): CommentReadModel;
}
