<?php

namespace Clean\Application\Port\Secondary;

interface ArticleRepository
{
    public function findIdBySlug(string $slug): ?int;
}
