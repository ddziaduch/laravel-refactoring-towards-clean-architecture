<?php

namespace Conduit\Article\Application\UseCase;

use Conduit\Article\Application\Port\Primary\ListTags;
use Conduit\Article\Application\Port\Secondary\ArticleQueries;

final readonly class ListTagsUseCase implements ListTags
{
    public function __construct(private ArticleQueries $articles) {}

    public function __invoke(): array
    {
        return $this->articles->tagNames();
    }
}
