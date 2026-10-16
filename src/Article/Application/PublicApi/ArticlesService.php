<?php

namespace Conduit\Article\Application\PublicApi;

use Conduit\Article\Application\Port\Secondary\ArticleRepository;
use Conduit\Article\PublicApi\Articles;

final readonly class ArticlesService implements Articles
{
    public function __construct(private ArticleRepository $articles) {}

    public function idOfSlug(string $slug): ?int
    {
        return $this->articles->findBySlug($slug)?->id;
    }
}
