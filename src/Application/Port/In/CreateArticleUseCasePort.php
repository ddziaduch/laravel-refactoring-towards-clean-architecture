<?php
declare(strict_types=1);

namespace Clean\Application\Port\In;

use Clean\Application\ReadModel\ArticleReadModel;

interface CreateArticleUseCasePort
{
    public function __invoke(
        int $authorId,
        string $title,
        string $description,
        string $body,
        string ...$tagList
    ): int;
}
