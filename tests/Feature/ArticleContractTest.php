<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class ArticleContractTest extends TestCase
{
    use RefreshDatabase;

    public function test_global_articles_use_default_pagination_order_and_total_count(): void
    {
        $author = $this->createUser('author');
        $now = Carbon::now();

        foreach (range(0, 22) as $index) {
            Article::factory()->for($author)->create([
                'title' => 'Article '.$index,
                'created_at' => $now->copy()->subMinutes($index),
                'updated_at' => $now->copy()->subMinutes($index),
            ]);
        }

        $this->getJson('/api/articles')
            ->assertOk()
            ->assertJsonCount(20, 'articles')
            ->assertJsonPath('articlesCount', 23)
            ->assertJsonPath('articles.0.title', 'Article 0')
            ->assertJsonMissingPath('articles.0.body');

        $this->getJson('/api/articles?limit=5&offset=2')
            ->assertOk()
            ->assertJsonCount(5, 'articles')
            ->assertJsonPath('articlesCount', 23)
            ->assertJsonPath('articles.0.title', 'Article 2');

        $this->getJson('/api/articles?limit=3')
            ->assertOk()
            ->assertJsonCount(3, 'articles');

        $this->getJson('/api/articles?offset=2')
            ->assertOk()
            ->assertJsonCount(20, 'articles');

        $this->getJson('/api/articles?limit=0&offset=-1')
            ->assertUnprocessable()
            ->assertJsonStructure(['errors' => ['limit', 'offset']]);
    }

    public function test_feed_is_protected_and_only_contains_followed_authors(): void
    {
        $reader = $this->createUser('reader');
        $followed = $this->createUser('followed');
        $other = $this->createUser('other');
        $reader->following()->attach($followed);

        Article::factory()->for($followed)->create(['title' => 'Followed article']);
        Article::factory()->for($other)->create(['title' => 'Other article']);
        Article::factory()->for($reader)->create(['title' => 'Own article']);

        $this->getJson('/api/articles/feed')
            ->assertUnauthorized()
            ->assertJsonStructure(['errors' => ['token']]);

        $this->getJson('/api/articles/feed', $this->conduitHeaders($reader))
            ->assertOk()
            ->assertJsonCount(1, 'articles')
            ->assertJsonPath('articlesCount', 1)
            ->assertJsonPath('articles.0.title', 'Followed article');
    }

    public function test_article_crud_tags_and_favorites_follow_the_contract(): void
    {
        $author = $this->createUser('author');
        $reader = $this->createUser('reader');
        $authorHeaders = $this->conduitHeaders($author);
        $readerHeaders = $this->conduitHeaders($reader);
        $payload = [
            'article' => [
                'title' => 'Contract article',
                'description' => 'Description',
                'body' => 'Body',
                'tagList' => ['laravel', 'architecture'],
            ],
        ];

        $created = $this->postJson('/api/articles', $payload, $authorHeaders)
            ->assertCreated()
            ->assertJsonPath('article.slug', 'contract-article')
            ->assertJsonPath('article.favoritesCount', 0)
            ->assertJsonPath('article.favorited', false)
            ->assertJsonCount(2, 'article.tagList');

        $duplicate = $this->postJson('/api/articles', $payload, $readerHeaders)
            ->assertCreated();

        $this->assertNotSame('contract-article', $duplicate->json('article.slug'));

        $this->putJson('/api/articles/contract-article', [
            'article' => ['title' => 'Renamed article'],
        ], $authorHeaders)->assertOk()
            ->assertJsonPath('article.slug', 'renamed-article')
            ->assertJsonCount(2, 'article.tagList');

        $this->postJson('/api/articles/renamed-article/favorite', [], $readerHeaders)
            ->assertOk()
            ->assertJsonPath('article.favorited', true)
            ->assertJsonPath('article.favoritesCount', 1);

        $this->postJson('/api/articles/renamed-article/favorite', [], $readerHeaders)
            ->assertOk()
            ->assertJsonPath('article.favoritesCount', 1);

        $this->assertDatabaseCount('article_user', 1);

        $this->putJson('/api/articles/renamed-article', [
            'article' => ['body' => 'Forbidden change'],
        ], $readerHeaders)->assertForbidden()
            ->assertExactJson(['errors' => ['article' => ['forbidden']]]);

        $this->deleteJson('/api/articles/renamed-article/favorite', [], $readerHeaders)
            ->assertOk()
            ->assertJsonPath('article.favoritesCount', 0);

        $this->deleteJson('/api/articles/renamed-article', [], $authorHeaders)
            ->assertNoContent();

        $this->getJson('/api/articles/renamed-article')
            ->assertNotFound()
            ->assertExactJson(['errors' => ['article' => ['not found']]]);

        $this->assertSame('contract-article', $created->json('article.slug'));
    }

    public function test_article_filters_counts_and_tags_endpoint_are_consistent(): void
    {
        $alice = $this->createUser('alice');
        $bob = $this->createUser('bob');
        $tag = Tag::factory()->create(['name' => 'php']);
        $aliceArticle = Article::factory()->for($alice)->create(['title' => 'Alice PHP']);
        $aliceArticle->tags()->attach($tag);
        Article::factory()->for($bob)->create(['title' => 'Bob article']);
        $aliceArticle->users()->attach($bob);

        $this->getJson('/api/articles?author=alice')
            ->assertOk()
            ->assertJsonCount(1, 'articles')
            ->assertJsonPath('articlesCount', 1)
            ->assertJsonPath('articles.0.favoritesCount', 1);

        $this->getJson('/api/articles?tag=php')
            ->assertOk()
            ->assertJsonCount(1, 'articles');

        $this->getJson('/api/articles?favorited=bob')
            ->assertOk()
            ->assertJsonCount(1, 'articles');

        $this->getJson('/api/tags')
            ->assertOk()
            ->assertJsonPath('tags.0', 'php');
    }
}
