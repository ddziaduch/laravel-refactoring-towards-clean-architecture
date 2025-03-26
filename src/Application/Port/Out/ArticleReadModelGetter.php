<?php

declare(strict_types=1);

namespace Clean\Application\Port\Out;

use Clean\Application\ReadModel\ArticleReadModel;

interface ArticleReadModelGetter
{
    public function get(int $id, ?int $currentUserId): ArticleReadModel;
}
