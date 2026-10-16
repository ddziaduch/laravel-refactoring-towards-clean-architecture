<?php

namespace Tests\Feature\Article;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class ListArticlesTest extends TestCase
{
    use RefreshDatabase;

    public function test_articles_use_default_pagination_newest_first_and_total_count(): void
    {
        $now = Carbon::parse('2026-10-01 12:00:00');
        $this->travelTo($now->copy()->subMinutes(22));
        $author = $this->registerUser('author');

        foreach (range(22, 0) as $index) {
            $this->travelTo($now->copy()->subMinutes($index));
            $this->publishArticle($author, 'Article '.$index);
        }

        $this->getJson('/api/articles')
            ->assertOk()
            ->assertJsonCount(20, 'articles')
            ->assertJsonPath('articlesCount', 23)
            ->assertJsonPath('articles.0.title', 'Article 0')
            ->assertJsonPath('articles.19.title', 'Article 19')
            ->assertJsonMissingPath('articles.0.body');

        $this->getJson('/api/articles?limit=5&offset=2')
            ->assertOk()
            ->assertJsonCount(5, 'articles')
            ->assertJsonPath('articlesCount', 23)
            ->assertJsonPath('articles.0.title', 'Article 2');

        $this->getJson('/api/articles?offset=20')
            ->assertOk()
            ->assertJsonCount(3, 'articles');
    }

    public function test_articles_with_the_same_creation_time_are_ordered_by_newest_id(): void
    {
        $this->travelTo(Carbon::parse('2026-10-01 12:00:00'));
        $author = $this->registerUser('author');
        $this->publishArticle($author, 'First');
        $this->publishArticle($author, 'Second');

        $this->getJson('/api/articles')
            ->assertJsonPath('articles.0.title', 'Second')
            ->assertJsonPath('articles.1.title', 'First');
    }

    public function test_pagination_is_validated(): void
    {
        $this->getJson('/api/articles?limit=0&offset=-1')
            ->assertUnprocessable()
            ->assertJsonStructure(['errors' => ['limit', 'offset']])
            ->assertJsonMissingPath('message');
    }

    public function test_list_item_has_the_contract_shape(): void
    {
        $this->travelTo(Carbon::parse('2026-10-01 12:34:56'));
        $author = $this->registerUser('author');
        $reader = $this->registerUser('reader');
        $slug = $this->publishArticle($author, 'Listed article', ['tagList' => ['php']]);
        $this->follow($reader, $author);
        $this->favorite($reader, $slug);

        $this->getJson('/api/articles', $reader->headers())
            ->assertOk()
            ->assertExactJson([
                'articles' => [[
                    'slug' => 'listed-article',
                    'title' => 'Listed article',
                    'description' => 'About Listed article',
                    'tagList' => ['php'],
                    'createdAt' => '2026-10-01T12:34:56.000000Z',
                    'updatedAt' => '2026-10-01T12:34:56.000000Z',
                    'favorited' => true,
                    'favoritesCount' => 1,
                    'author' => [
                        'username' => 'author',
                        'bio' => null,
                        'image' => null,
                        'following' => true,
                    ],
                ]],
                'articlesCount' => 1,
            ]);
    }

    public function test_articles_can_be_filtered(): void
    {
        $alice = $this->registerUser('alice');
        $bob = $this->registerUser('bob');
        $carol = $this->registerUser('carol');
        $alicePhp = $this->publishArticle($alice, 'Alice PHP', ['tagList' => ['php']]);
        $this->publishArticle($alice, 'Alice Go', ['tagList' => ['go']]);
        $bobPhp = $this->publishArticle($bob, 'Bob PHP', ['tagList' => ['php']]);
        $this->favorite($bob, $alicePhp);
        $this->favorite($carol, $alicePhp);
        $this->favorite($carol, $bobPhp);

        $this->getJson('/api/articles?author=alice')
            ->assertOk()
            ->assertJsonPath('articlesCount', 2)
            ->assertJsonPath('articles.*.author.username', ['alice', 'alice']);

        $this->getJson('/api/articles?tag=php')
            ->assertOk()
            ->assertJsonPath('articlesCount', 2)
            ->assertJsonPath('articles.*.title', ['Bob PHP', 'Alice PHP'])
            ->assertJsonPath('articles.1.favoritesCount', 2);

        $this->getJson('/api/articles?favorited=bob')
            ->assertOk()
            ->assertJsonPath('articlesCount', 1)
            ->assertJsonPath('articles.0.title', 'Alice PHP');

        $this->getJson('/api/articles?favorited=carol&author=bob&tag=php')
            ->assertOk()
            ->assertJsonPath('articlesCount', 1)
            ->assertJsonPath('articles.0.title', 'Bob PHP');

        $this->getJson('/api/articles?author=nobody')
            ->assertOk()
            ->assertExactJson(['articles' => [], 'articlesCount' => 0]);

        $this->getJson('/api/articles?favorited=nobody')
            ->assertOk()
            ->assertExactJson(['articles' => [], 'articlesCount' => 0]);

        $this->getJson('/api/articles?tag=missing')
            ->assertOk()
            ->assertExactJson(['articles' => [], 'articlesCount' => 0]);
    }

    public function test_feed_requires_a_token_and_only_contains_followed_authors(): void
    {
        $reader = $this->registerUser('reader');
        $followed = $this->registerUser('followed');
        $other = $this->registerUser('other');
        $this->follow($reader, $followed);
        $this->publishArticle($followed, 'Followed article');
        $this->publishArticle($followed, 'Another followed article');
        $this->publishArticle($other, 'Other article');
        $this->publishArticle($reader, 'Own article');

        $this->getJson('/api/articles/feed')
            ->assertUnauthorized()
            ->assertExactJson(['errors' => ['token' => ['is missing']]]);

        $this->getJson('/api/articles/feed', $reader->headers())
            ->assertOk()
            ->assertJsonCount(2, 'articles')
            ->assertJsonPath('articlesCount', 2)
            ->assertJsonPath('articles.0.title', 'Another followed article')
            ->assertJsonPath('articles.0.author.following', true);

        $this->getJson('/api/articles/feed?limit=1&offset=1', $reader->headers())
            ->assertOk()
            ->assertJsonCount(1, 'articles')
            ->assertJsonPath('articlesCount', 2)
            ->assertJsonPath('articles.0.title', 'Followed article');

        $this->getJson('/api/articles/feed', $other->headers())
            ->assertOk()
            ->assertExactJson(['articles' => [], 'articlesCount' => 0]);
    }
}
