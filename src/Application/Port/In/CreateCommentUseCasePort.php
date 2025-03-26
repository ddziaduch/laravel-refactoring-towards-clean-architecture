<?php

namespace Clean\Application\Port\In;

interface CreateCommentUseCasePort
{
    public function create(string $articleSlug, string $commentBody, int $authorId): int;
}
