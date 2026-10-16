<?php

namespace Conduit\Article\Adapter\Secondary\Database;

use Conduit\Article\Application\Port\Secondary\ArticleCriteria;
use Conduit\Article\Application\Port\Secondary\ArticlePage;
use Conduit\Article\Application\Port\Secondary\ArticleQueries;
use Conduit\Article\Application\Port\Secondary\ArticleRecord;
use DateTimeImmutable;
use DateTimeZone;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;

/**
 * Reads only articles, tags, article_tag and article_user: the tables this
 * module owns. There is no join to users, authors are resolved elsewhere.
 */
final readonly class DatabaseArticleQueries implements ArticleQueries
{
    public function __construct(private ConnectionInterface $connection) {}

    public function page(ArticleCriteria $criteria, int $limit, int $offset, ?int $viewerId): ArticlePage
    {
        $query = $this->connection->table('articles')
            ->when($criteria->tag !== null, fn (Builder $query) => $query->whereExists(
                fn (Builder $tagged) => $tagged->selectRaw('1')
                    ->from('article_tag')
                    ->join('tags', 'tags.id', '=', 'article_tag.tag_id')
                    ->whereColumn('article_tag.article_id', 'articles.id')
                    ->where('tags.name', $criteria->tag),
            ))
            ->when($criteria->authorIds !== null, fn (Builder $query) => $query->whereIn('user_id', $criteria->authorIds))
            ->when($criteria->favoritedBy !== null, fn (Builder $query) => $query->whereExists(
                fn (Builder $favorited) => $favorited->selectRaw('1')
                    ->from('article_user')
                    ->whereColumn('article_user.article_id', 'articles.id')
                    ->where('article_user.user_id', $criteria->favoritedBy),
            ));

        $total = (clone $query)->count();

        $rows = $query
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->offset($offset)
            ->limit($limit)
            ->get();

        return new ArticlePage($this->records($rows, $viewerId), $total);
    }

    public function bySlug(string $slug, ?int $viewerId): ?ArticleRecord
    {
        return $this->records($this->connection->table('articles')->where('slug', $slug)->get(), $viewerId)[0] ?? null;
    }

    public function tagNames(): array
    {
        return $this->connection->table('tags')->orderBy('id')->pluck('name')->all();
    }

    /**
     * Loads tags and favorites for the whole page in a constant number of queries.
     *
     * @return list<ArticleRecord>
     */
    private function records(Collection $rows, ?int $viewerId): array
    {
        $ids = $rows->pluck('id')->all();

        $tags = $this->connection->table('article_tag')
            ->join('tags', 'tags.id', '=', 'article_tag.tag_id')
            ->whereIn('article_tag.article_id', $ids)
            ->orderBy('tags.id')
            ->get(['article_tag.article_id', 'tags.name'])
            ->groupBy('article_id');

        $favoritesCounts = $this->connection->table('article_user')
            ->whereIn('article_id', $ids)
            ->groupBy('article_id')
            ->selectRaw('article_id, count(*) as favorites_count')
            ->pluck('favorites_count', 'article_id');

        $favoritedByViewer = $viewerId === null ? collect() : $this->connection->table('article_user')
            ->whereIn('article_id', $ids)
            ->where('user_id', $viewerId)
            ->pluck('article_id')
            ->flip();

        return $rows->map(fn (object $row): ArticleRecord => new ArticleRecord(
            $row->slug,
            $row->title,
            $row->description,
            $row->body,
            $tags->get($row->id, collect())->pluck('name')->all(),
            $this->date($row->created_at),
            $this->date($row->updated_at),
            $favoritedByViewer->has($row->id),
            (int) $favoritesCounts->get($row->id, 0),
            (int) $row->user_id,
        ))->all();
    }

    private function date(string $value): DateTimeImmutable
    {
        return new DateTimeImmutable($value, new DateTimeZone('UTC'));
    }
}
