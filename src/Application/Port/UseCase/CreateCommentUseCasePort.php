<?php

namespace Clean\Application\Port\UseCase;

use Clean\Application\ReadModel\CommentReadModel;

interface CreateCommentUseCasePort
{
    public function create(int $articleId, string $commentBody, int $authorId): CommentReadModel;
}
