<?php

declare(strict_types=1);

namespace Clean\Application\Port\Out;

use Clean\Application\Exception\ArticleAlreadyExist;
use Clean\Domain\Entity\Article;

interface ArticleRepository
{
    /**
     * @throws ArticleAlreadyExist
     */
    public function save(Article $article): \App\Models\Article;
}
