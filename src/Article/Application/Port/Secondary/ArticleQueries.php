<?php

namespace Conduit\Article\Application\Port\Secondary;

/**
 * Read side of the module. It only touches the module's own tables, so it
 * knows authors by id and nothing else about them.
 */
interface ArticleQueries
{
    public function page(ArticleCriteria $criteria, int $limit, int $offset, ?int $viewerId): ArticlePage;

    public function bySlug(string $slug, ?int $viewerId): ?ArticleRecord;

    /**
     * @return list<string>
     */
    public function tagNames(): array;
}
