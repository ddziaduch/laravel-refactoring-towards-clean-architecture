<?php

namespace Tests\Feature;

use App\Models\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreateCommentCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_comment_an_article_from_the_command_line(): void
    {
        $commenter = $this->createUser('commenter');
        $article = Article::factory()->for($this->createUser('author'))->create();

        $this->artisan('comment:create', [
            'article' => $article->slug,
            'author' => $commenter->id,
            'body' => 'A useful comment',
        ])->assertSuccessful();

        $this->assertDatabaseHas('comments', [
            'article_id' => $article->id,
            'user_id' => $commenter->id,
            'body' => 'A useful comment',
        ]);
    }

    public function test_command_fails_for_a_missing_article(): void
    {
        $commenter = $this->createUser('commenter');

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
