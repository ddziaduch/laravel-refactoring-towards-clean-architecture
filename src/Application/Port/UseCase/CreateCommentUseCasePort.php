<?php

namespace Clean\Application\Port\UseCase;

interface CreateCommentUseCasePort
{
    public function create(string $articleSlug, string $commentBody, int $authorId): int;
}
