<?php

declare(strict_types=1);

namespace Clean\Application\Port\Out;

use Clean\Application\ReadModel\ArticleReadModel;

interface ArticleReadModelFinder
{
    public function bySlug(string $slug): ArticleReadModel;
}
