<?php

namespace Tests\Unit;

use Clean\Application\Port\Secondary\ArticleRepository;

final readonly class InMemoryArticleRepository implements ArticleRepository
{
    /**
     * @param  array<string, int>  $idsBySlug
     */
    public function __construct(private array $idsBySlug) {}

    public function findIdBySlug(string $slug): ?int
    {
        return $this->idsBySlug[$slug] ?? null;
    }
}
