<?php

namespace Conduit\Article\Application\Service;

use Conduit\Article\Application\Port\Secondary\ArticleRepository;
use Conduit\Article\Application\Port\Secondary\Slugger;

/**
 * "My title" becomes "my-title", then "my-title-1", "my-title-2"... when taken.
 */
final readonly class UniqueSlug
{
    public function __construct(
        private Slugger $slugger,
        private ArticleRepository $articles,
    ) {}

    public function for(string $title, ?int $articleId = null): string
    {
        $baseSlug = $this->slugger->slugify($title);
        $slug = $baseSlug;
        $suffix = 1;

        while ($this->articles->slugExists($slug, $articleId)) {
            $slug = $baseSlug.'-'.$suffix++;
        }

        return $slug;
    }
}
