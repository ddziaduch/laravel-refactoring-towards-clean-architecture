<?php

namespace Conduit\Engagement\Adapter\Secondary\Article;

use Conduit\Article\PublicApi\Articles;
use Conduit\Engagement\Application\Port\Secondary\ArticleLookup;

/**
 * Anti-corruption layer towards the Article module.
 */
final readonly class ArticleApiLookup implements ArticleLookup
{
    public function __construct(private Articles $articles) {}

    public function idOfSlug(string $slug): ?int
    {
        return $this->articles->idOfSlug($slug);
    }
}
