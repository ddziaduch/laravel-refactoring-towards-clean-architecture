<?php

namespace Tests\Feature\Article;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class ArticleTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_publish_an_article(): void
    {
        $this->travelTo(Carbon::parse('2026-10-01 12:34:56'));
        $author = $this->registerUser('author');

        $this->postJson('/api/articles', [
            'article' => [
                'title' => 'How to train your dragon',
                'description' => 'Ever wonder how?',
                'body' => 'You have to believe',
                'tagList' => ['dragons', 'training'],
            ],
        ], $author->headers())
            ->assertCreated()
            ->assertExactJson([
                'article' => [
                    'slug' => 'how-to-train-your-dragon',
                    'title' => 'How to train your dragon',
                    'description' => 'Ever wonder how?',
                    'body' => 'You have to believe',
                    'tagList' => ['dragons', 'training'],
                    'createdAt' => '2026-10-01T12:34:56.000000Z',
                    'updatedAt' => '2026-10-01T12:34:56.000000Z',
                    'favorited' => false,
                    'favoritesCount' => 0,
                    'author' => [
                        'username' => 'author',
                        'bio' => null,
                        'image' => null,
                        'following' => false,
                    ],
                ],
            ]);
    }

    public function test_publishing_requires_a_token_and_valid_payload(): void
    {
        $this->postJson('/api/articles', ['article' => ['title' => 'T', 'description' => 'D', 'body' => 'B']])
            ->assertUnauthorized()
            ->assertExactJson(['errors' => ['token' => ['is missing']]]);

        $author = $this->registerUser('author');

        $this->postJson('/api/articles', ['article' => ['title' => '']], $author->headers())
            ->assertUnprocessable()
            ->assertExactJson([
                'errors' => [
                    'title' => ["can't be blank"],
                    'description' => ["can't be blank"],
                    'body' => ["can't be blank"],
                ],
            ]);

        $this->assertDatabaseCount('articles', 0);
    }

    public function test_slugs_are_unique(): void
    {
        $author = $this->registerUser('author');
        $other = $this->registerUser('other');

        $this->assertSame('same-title', $this->publishArticle($author, 'Same title'));
        $this->assertSame('same-title-1', $this->publishArticle($other, 'Same title'));
        $this->assertSame('same-title-2', $this->publishArticle($author, 'Same title'));
    }

    public function test_article_can_be_shown_from_the_perspective_of_a_viewer(): void
    {
        $author = $this->registerUser('author');
        $reader = $this->registerUser('reader');
        $slug = $this->publishArticle($author, 'Shown article', ['tagList' => ['php']]);
        $this->follow($reader, $author);
        $this->favorite($reader, $slug);

        $this->getJson('/api/articles/'.$slug)
            ->assertOk()
            ->assertJsonPath('article.slug', $slug)
            ->assertJsonPath('article.body', 'Body of Shown article')
            ->assertJsonPath('article.tagList', ['php'])
            ->assertJsonPath('article.favorited', false)
            ->assertJsonPath('article.favoritesCount', 1)
            ->assertJsonPath('article.author.following', false);

        $this->getJson('/api/articles/'.$slug, $reader->headers())
            ->assertOk()
            ->assertJsonPath('article.favorited', true)
            ->assertJsonPath('article.favoritesCount', 1)
            ->assertJsonPath('article.author.following', true);
    }

    public function test_missing_article_is_not_found(): void
    {
        $this->getJson('/api/articles/missing')
            ->assertNotFound()
            ->assertExactJson(['errors' => ['article' => ['not found']]]);

        $author = $this->registerUser('author');

        $this->putJson('/api/articles/missing', ['article' => ['body' => 'B']], $author->headers())
            ->assertNotFound()
            ->assertExactJson(['errors' => ['article' => ['not found']]]);

        $this->deleteJson('/api/articles/missing', [], $author->headers())
            ->assertNotFound()
            ->assertExactJson(['errors' => ['article' => ['not found']]]);

        $this->postJson('/api/articles/missing/favorite', [], $author->headers())
            ->assertNotFound()
            ->assertExactJson(['errors' => ['article' => ['not found']]]);
    }

    public function test_author_can_update_an_article(): void
    {
        $author = $this->registerUser('author');
        $slug = $this->publishArticle($author, 'Original title', ['tagList' => ['a', 'b']]);

        $this->putJson('/api/articles/'.$slug, [
            'article' => ['body' => 'Updated body'],
        ], $author->headers())
            ->assertOk()
            ->assertJsonPath('article.slug', 'original-title')
            ->assertJsonPath('article.title', 'Original title')
            ->assertJsonPath('article.body', 'Updated body')
            ->assertJsonPath('article.tagList', ['a', 'b']);

        $this->putJson('/api/articles/'.$slug, [
            'article' => ['title' => 'Renamed title', 'description' => 'New description', 'tagList' => ['c']],
        ], $author->headers())
            ->assertOk()
            ->assertJsonPath('article.slug', 'renamed-title')
            ->assertJsonPath('article.description', 'New description')
            ->assertJsonPath('article.tagList', ['c']);

        $this->getJson('/api/articles/original-title')->assertNotFound();
        $this->getJson('/api/articles/renamed-title')->assertOk();
    }

    public function test_keeping_the_title_keeps_the_slug(): void
    {
        $author = $this->registerUser('author');
        $slug = $this->publishArticle($author, 'Stable title');

        $this->putJson('/api/articles/'.$slug, [
            'article' => ['title' => 'Stable title'],
        ], $author->headers())
            ->assertOk()
            ->assertJsonPath('article.slug', 'stable-title');
    }

    public function test_only_the_author_can_update_or_delete_an_article(): void
    {
        $author = $this->registerUser('author');
        $stranger = $this->registerUser('stranger');
        $slug = $this->publishArticle($author, 'Protected article');

        $this->putJson('/api/articles/'.$slug, ['article' => ['body' => 'Hacked']], $stranger->headers())
            ->assertForbidden()
            ->assertExactJson(['errors' => ['article' => ['forbidden']]]);

        $this->deleteJson('/api/articles/'.$slug, [], $stranger->headers())
            ->assertForbidden()
            ->assertExactJson(['errors' => ['article' => ['forbidden']]]);

        $this->putJson('/api/articles/'.$slug, ['article' => ['body' => 'Hacked']])
            ->assertUnauthorized();

        $this->getJson('/api/articles/'.$slug)->assertJsonPath('article.body', 'Body of Protected article');
    }

    public function test_update_is_validated(): void
    {
        $author = $this->registerUser('author');
        $slug = $this->publishArticle($author, 'Validated article');

        $this->putJson('/api/articles/'.$slug, ['article' => []], $author->headers())
            ->assertUnprocessable()
            ->assertExactJson(['errors' => ['article' => ["can't be blank"]]]);
    }

    public function test_author_can_delete_an_article_with_its_comments_and_favorites(): void
    {
        $author = $this->registerUser('author');
        $reader = $this->registerUser('reader');
        $slug = $this->publishArticle($author, 'Doomed article', ['tagList' => ['php']]);
        $this->favorite($reader, $slug);
        $this->comment($reader, $slug, 'Nice');

        $this->deleteJson('/api/articles/'.$slug, [], $author->headers())->assertNoContent();

        $this->getJson('/api/articles/'.$slug)->assertNotFound();
        $this->assertDatabaseCount('articles', 0);
        $this->assertDatabaseCount('article_user', 0);
        $this->assertDatabaseCount('article_tag', 0);
        $this->assertDatabaseCount('comments', 0);
        $this->getJson('/api/tags')->assertExactJson(['tags' => ['php']]);
    }
}
