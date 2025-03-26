<?php

declare(strict_types=1);

namespace Clean\Application\Port\Out;

use Clean\Application\Exception\ArticleDoesNotExist;
use Clean\Application\Exception\ArticleAlreadyExist;
use Clean\Domain\Entity\Article;

interface ArticleRepository
{
    /**
     * @throws ArticleDoesNotExist
     */
    public function getBySlug(string $articleSlug): Article;

    /**
     * @throws ArticleAlreadyExist
     */
    public function save(Article $article): void;
}
