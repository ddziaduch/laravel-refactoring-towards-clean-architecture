<?php

namespace Tests\Feature\Article;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FavoriteArticleTest extends TestCase
{
    use RefreshDatabase;

    public function test_favorite_and_unfavorite_are_idempotent(): void
    {
        $author = $this->registerUser('author');
        $reader = $this->registerUser('reader');
        $slug = $this->publishArticle($author, 'Favorite article');

        $this->postJson('/api/articles/'.$slug.'/favorite', [], $reader->headers())
            ->assertOk()
            ->assertJsonPath('article.slug', $slug)
            ->assertJsonPath('article.body', 'Body of Favorite article')
            ->assertJsonPath('article.favorited', true)
            ->assertJsonPath('article.favoritesCount', 1);

        $this->postJson('/api/articles/'.$slug.'/favorite', [], $reader->headers())
            ->assertOk()
            ->assertJsonPath('article.favoritesCount', 1);

        $this->postJson('/api/articles/'.$slug.'/favorite', [], $author->headers())
            ->assertOk()
            ->assertJsonPath('article.favoritesCount', 2);

        $this->deleteJson('/api/articles/'.$slug.'/favorite', [], $reader->headers())
            ->assertOk()
            ->assertJsonPath('article.favorited', false)
            ->assertJsonPath('article.favoritesCount', 1);

        $this->deleteJson('/api/articles/'.$slug.'/favorite', [], $reader->headers())
            ->assertOk()
            ->assertJsonPath('article.favoritesCount', 1);

        $this->assertDatabaseCount('article_user', 1);
    }

    public function test_favoriting_requires_a_token(): void
    {
        $author = $this->registerUser('author');
        $slug = $this->publishArticle($author, 'Favorite article');

        $this->postJson('/api/articles/'.$slug.'/favorite')
            ->assertUnauthorized()
            ->assertExactJson(['errors' => ['token' => ['is missing']]]);

        $this->deleteJson('/api/articles/'.$slug.'/favorite')
            ->assertUnauthorized()
            ->assertExactJson(['errors' => ['token' => ['is missing']]]);
    }
}
