<?php
declare(strict_types=1);

namespace Clean\Application\Port\In;

use App\Models\Article;

interface CreateArticleUseCasePort
{
    public function __invoke(
        int $authorId,
        string $title,
        string $description,
        string $body,
        string ...$tagList
    ): Article;
}
