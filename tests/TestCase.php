<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\DB;

abstract class TestCase extends BaseTestCase
{
    /**
     * Registers a user through the public API, so fixtures never depend on
     * the internals of any module.
     */
    protected function registerUser(string $username, string $password = 'password123'): TestUser
    {
        $token = $this->postJson('/api/users', [
            'user' => [
                'username' => $username,
                'email' => $username.'@example.com',
                'password' => $password,
            ],
        ])->assertCreated()->json('user.token');

        return new TestUser(
            (int) DB::table('users')->where('username', $username)->value('id'),
            $username,
            $username.'@example.com',
            $token,
        );
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    protected function publishArticle(TestUser $author, string $title, array $attributes = []): string
    {
        return $this->postJson('/api/articles', [
            'article' => $attributes + [
                'title' => $title,
                'description' => 'About '.$title,
                'body' => 'Body of '.$title,
            ],
        ], $author->headers())->assertCreated()->json('article.slug');
    }

    protected function follow(TestUser $follower, TestUser $followed): void
    {
        $this->postJson('/api/profiles/'.$followed->username.'/follow', [], $follower->headers())->assertOk();
    }

    protected function favorite(TestUser $reader, string $slug): void
    {
        $this->postJson('/api/articles/'.$slug.'/favorite', [], $reader->headers())->assertOk();
    }

    protected function comment(TestUser $commenter, string $slug, string $body): int
    {
        return $this->postJson('/api/articles/'.$slug.'/comments', [
            'comment' => ['body' => $body],
        ], $commenter->headers())->assertCreated()->json('comment.id');
    }

    public function getJson($uri, array $headers = [], $options = 0)
    {
        $this->resetAuthForNextRequest();

        return parent::getJson($uri, $headers, $options);
    }

    public function postJson($uri, array $data = [], array $headers = [], $options = 0)
    {
        $this->resetAuthForNextRequest();

        return parent::postJson($uri, $data, $headers, $options);
    }

    public function putJson($uri, array $data = [], array $headers = [], $options = 0)
    {
        $this->resetAuthForNextRequest();

        return parent::putJson($uri, $data, $headers, $options);
    }

    public function deleteJson($uri, array $data = [], array $headers = [], $options = 0)
    {
        $this->resetAuthForNextRequest();

        return parent::deleteJson($uri, $data, $headers, $options);
    }

    private function resetAuthForNextRequest(): void
    {
        $this->app['auth']->forgetGuards();
        $this->app['tymon.jwt']->unsetToken();
    }
}
