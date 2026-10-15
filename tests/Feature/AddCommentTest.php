<?php

namespace Tests\Feature;

use App\Models\Article;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AddCommentTest extends TestCase
{
    use RefreshDatabase;

    public function test_happy_path(): void
    {
        Carbon::setTestNow('2026-10-15T00:00:00.000000Z');

        $user = $this->createUser('john');

        $this->postJson('/api/users/login', [
            'user' => ['email' => 'john@example.com', 'password' => 'secret'],
        ])->assertOk();

        $article = Article::factory()->create([
            'title' => 'Test Article',
            'description' => 'Lorem ipsum',
            'body' => 'Lorem ipsum as well',
        ]);

        $response = $this->postJson(
            uri: '/api/articles/' . $article->slug . '/comments',
            data: [ 'comment' => [ 'body' => 'Hello world!' ], ],
            headers: $this->conduitHeaders($user),
        );

        $response->assertCreated();

        $username = $response->json('comment.author.username');
        self::assertSame('john', $username);

        $body = $response->json('comment.body');
        self::assertSame('Hello world!', $body);

        $createdAt = $response->json('comment.createdAt');
        self::assertSame('2026-10-15T00:00:00.000000Z', $createdAt);

        $updatedAt = $response->json('comment.updatedAt');
        self::assertSame('2026-10-15T00:00:00.000000Z', $updatedAt);
    }

    public function test_empty_body(): void
    {
        Carbon::setTestNow('2026-10-15T00:00:00.000000Z');

        $user = $this->createUser('john');

        $this->postJson('/api/users/login', [
            'user' => ['email' => 'john@example.com', 'password' => 'secret'],
        ])->assertOk();

        $article = Article::factory()->create([
            'title' => 'Test Article',
            'description' => 'Lorem ipsum',
            'body' => 'Lorem ipsum as well',
        ]);

        $response = $this->postJson(
            uri: '/api/articles/' . $article->slug . '/comments',
            data: [],
            headers: $this->conduitHeaders($user),
        );

        $response->assertStatus(422);
    }

    public function test_not_authenticated_in(): void
    {
        Carbon::setTestNow('2026-10-15T00:00:00.000000Z');

        $article = Article::factory()->create([
            'title' => 'Test Article',
            'description' => 'Lorem ipsum',
            'body' => 'Lorem ipsum as well',
        ]);

        $response = $this->postJson(
            uri: '/api/articles/' . $article->slug . '/comments',
            data: [ 'comment' => [ 'body' => 'Hello world!' ], ],
        );

        $response->assertStatus(401);
    }

    public function test_non_existing_article_slug(): void
    {
        Carbon::setTestNow('2026-10-15T00:00:00.000000Z');

        $user = $this->createUser('john');

        $this->postJson('/api/users/login', [
            'user' => ['email' => 'john@example.com', 'password' => 'secret'],
        ])->assertOk();

        $response = $this->postJson(
            uri: '/api/articles/bullshit/comments',
            data: [ 'comment' => [ 'body' => 'Hello world!' ], ],
            headers: $this->conduitHeaders($user),
        );

        $response->assertStatus(404);
    }
}
