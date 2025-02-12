<?php

namespace Clean\Application\Port\UseCase;

interface CreateCommentUseCasePort
{
    public function create(int $articleId, string $commentBody, int $authorId): int;
}
