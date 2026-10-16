<?php

namespace Tests\Feature\Engagement;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AddCommentCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_comment_an_article_from_the_command_line(): void
    {
        $commenter = $this->registerUser('commenter');
        $slug = $this->publishArticle($this->registerUser('author'), 'Commented article');

        $this->artisan('comment:create', [
            'article' => $slug,
            'author' => $commenter->id,
            'body' => 'A useful comment',
        ])->assertSuccessful();

        $this->getJson('/api/articles/'.$slug.'/comments')
            ->assertJsonPath('comments.0.body', 'A useful comment')
            ->assertJsonPath('comments.0.author.username', 'commenter');
    }

    public function test_command_fails_for_a_missing_article(): void
    {
        $commenter = $this->registerUser('commenter');

        $this->artisan('comment:create', [
            'article' => 'missing-article',
            'author' => $commenter->id,
            'body' => 'A useful comment',
        ])
            ->expectsOutput('Article "missing-article" not found.')
            ->assertFailed();

        $this->assertDatabaseCount('comments', 0);
    }
}
