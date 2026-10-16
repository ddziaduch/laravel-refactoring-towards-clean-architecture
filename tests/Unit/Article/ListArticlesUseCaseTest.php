<?php

namespace Tests\Unit\Article;

use Conduit\Article\Application\Port\Primary\ArticleFilter;
use Conduit\Article\Application\Port\Secondary\ArticleCriteria;
use Conduit\Article\Application\Port\Secondary\ArticlePage;
use Conduit\Article\Application\Port\Secondary\ArticleQueries;
use Conduit\Article\Application\Port\Secondary\ArticleRecord;
use Conduit\Article\Application\Port\Secondary\AuthorProfiles;
use Conduit\Article\Application\Port\Secondary\Followings;
use Conduit\Article\Application\ReadModel\AuthorReadModel;
use Conduit\Article\Application\Service\ArticleAssembler;
use Conduit\Article\Application\UseCase\GetFeedUseCase;
use Conduit\Article\Application\UseCase\ListArticlesUseCase;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

/**
 * The Article module is tested without the Profile module: authors and
 * followings come from in-memory implementations of Article's own ports.
 */
class ListArticlesUseCaseTest extends TestCase
{
    private SpyArticleQueries $queries;

    private FakeAuthors $authors;

    protected function setUp(): void
    {
        $this->queries = new SpyArticleQueries([$this->record('first', authorId: 7), $this->record('second', authorId: 8)]);
        $this->authors = new FakeAuthors(['alice' => 7, 'bob' => 8], followedBy: [1 => [8]]);
    }

    public function test_usernames_are_translated_to_ids_before_querying(): void
    {
        $this->listArticles()(new ArticleFilter(tag: 'php', author: 'alice', favoritedBy: 'bob', limit: 5, offset: 10), null);

        $this->assertEquals(new ArticleCriteria('php', [7], 8), $this->queries->criteria);
        $this->assertSame([5, 10], [$this->queries->limit, $this->queries->offset]);
    }

    public function test_an_unknown_username_matches_nothing_without_querying(): void
    {
        $list = $this->listArticles()(new ArticleFilter(author: 'nobody'), null);

        $this->assertSame(0, $list->count);
        $this->assertNull($this->queries->criteria);
    }

    public function test_authors_are_fetched_in_one_batch_from_the_viewer_perspective(): void
    {
        $list = $this->listArticles()(new ArticleFilter, 1);

        $this->assertSame(2, $list->count);
        $this->assertSame(['alice', 'bob'], array_map(fn ($article) => $article->author->username, $list->articles));
        $this->assertSame([false, true], array_map(fn ($article) => $article->author->following, $list->articles));
        $this->assertSame([[[7, 8], 1]], $this->authors->calls);
    }

    public function test_feed_only_queries_followed_authors(): void
    {
        $feed = new GetFeedUseCase($this->authors, $this->queries, new ArticleAssembler($this->authors));

        $feed(1, 20, 0);

        $this->assertEquals(new ArticleCriteria(authorIds: [8]), $this->queries->criteria);
    }

    public function test_feed_of_a_reader_following_nobody_is_empty_without_querying(): void
    {
        $feed = new GetFeedUseCase($this->authors, $this->queries, new ArticleAssembler($this->authors));

        $this->assertSame(0, $feed(2, 20, 0)->count);
        $this->assertNull($this->queries->criteria);
    }

    private function listArticles(): ListArticlesUseCase
    {
        return new ListArticlesUseCase($this->queries, $this->authors, new ArticleAssembler($this->authors));
    }

    private function record(string $slug, int $authorId): ArticleRecord
    {
        $date = new DateTimeImmutable('2026-10-01 12:00:00');

        return new ArticleRecord($slug, $slug, 'description', 'body', [], $date, $date, false, 0, $authorId);
    }
}

final class SpyArticleQueries implements ArticleQueries
{
    public ?ArticleCriteria $criteria = null;

    public ?int $limit = null;

    public ?int $offset = null;

    /**
     * @param  list<ArticleRecord>  $records
     */
    public function __construct(private readonly array $records) {}

    public function page(ArticleCriteria $criteria, int $limit, int $offset, ?int $viewerId): ArticlePage
    {
        [$this->criteria, $this->limit, $this->offset] = [$criteria, $limit, $offset];

        return new ArticlePage($this->records, count($this->records));
    }

    public function bySlug(string $slug, ?int $viewerId): ?ArticleRecord
    {
        return null;
    }

    public function tagNames(): array
    {
        return [];
    }
}

final class FakeAuthors implements AuthorProfiles, Followings
{
    /**
     * @var list<array{list<int>, ?int}>
     */
    public array $calls = [];

    /**
     * @param  array<string, int>  $idsByUsername
     * @param  array<int, list<int>>  $followedBy
     */
    public function __construct(
        private readonly array $idsByUsername,
        private readonly array $followedBy,
    ) {}

    public function of(array $authorIds, ?int $viewerId): array
    {
        $this->calls[] = [$authorIds, $viewerId];
        $authors = [];

        foreach ($authorIds as $id) {
            $authors[$id] = new AuthorReadModel(
                array_search($id, $this->idsByUsername, true),
                null,
                null,
                in_array($id, $this->followedBy($viewerId ?? 0), true),
            );
        }

        return $authors;
    }

    public function idOfUsername(string $username): ?int
    {
        return $this->idsByUsername[$username] ?? null;
    }

    public function followedBy(int $readerId): array
    {
        return $this->followedBy[$readerId] ?? [];
    }
}
