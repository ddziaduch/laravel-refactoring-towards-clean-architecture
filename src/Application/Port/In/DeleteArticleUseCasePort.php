<?php

declare(strict_types = 1);

namespace Clean\Application\Port\In;

interface DeleteArticleUseCasePort
{
    public function handle(int $authorId, string $articleSlug): void;
}
