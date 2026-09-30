<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class CreateCommentTest extends TestCase
{
    use RefreshDatabase;

    private User $commenter;

    private Article $article;

    protected function setUp(): void
    {
        parent::setUp();

        $this->commenter = $this->createUser('commenter');
        $this->article = Article::factory()->for($this->createUser('author'))->create();
    }

    public function test_user_can_comment_an_article(): void
    {
        $this->travelTo(Carbon::parse('2026-10-01 12:34:56'));

        $response = $this->postJson(
            '/api/articles/'.$this->article->slug.'/comments',
            ['comment' => ['body' => 'A useful comment']],
            $this->conduitHeaders($this->commenter),
        )->assertCreated();

        $response->assertExactJson([
            'comment' => [
                'id' => $response->json('comment.id'),
                'createdAt' => '2026-10-01T12:34:56.000000Z',
                'updatedAt' => '2026-10-01T12:34:56.000000Z',
                'body' => 'A useful comment',
                'author' => [
                    'username' => 'commenter',
                    'bio' => $this->commenter->bio,
                    'image' => $this->commenter->image,
                    'following' => false,
                ],
            ],
        ]);

        $this->assertDatabaseHas('comments', [
            'id' => $response->json('comment.id'),
            'article_id' => $this->article->id,
            'user_id' => $this->commenter->id,
            'body' => 'A useful comment',
        ]);
    }

    public function test_each_request_comments_as_its_own_user(): void
    {
        $uri = '/api/articles/'.$this->article->slug.'/comments';
        $otherCommenter = $this->createUser('other-commenter');

        $this->postJson($uri, ['comment' => ['body' => 'First']], $this->conduitHeaders($this->commenter))
            ->assertCreated()
            ->assertJsonPath('comment.author.username', 'commenter');

        $this->postJson($uri, ['comment' => ['body' => 'Second']], $this->conduitHeaders($otherCommenter))
            ->assertCreated()
            ->assertJsonPath('comment.author.username', 'other-commenter');
    }

    public function test_guest_cannot_comment_an_article(): void
    {
        $this->postJson(
            '/api/articles/'.$this->article->slug.'/comments',
            ['comment' => ['body' => 'A useful comment']],
        )
            ->assertUnauthorized()
            ->assertExactJson(['errors' => ['token' => ['is missing']]]);

        $this->assertDatabaseCount('comments', 0);
    }

    public function test_user_cannot_comment_a_missing_article(): void
    {
        $this->postJson(
            '/api/articles/missing-article/comments',
            ['comment' => ['body' => 'A useful comment']],
            $this->conduitHeaders($this->commenter),
        )
            ->assertNotFound()
            ->assertExactJson(['errors' => ['article' => ['not found']]]);

        $this->assertDatabaseCount('comments', 0);
    }

    public function test_comment_body_is_required(): void
    {
        $this->postJson(
            '/api/articles/'.$this->article->slug.'/comments',
            ['comment' => ['body' => '']],
            $this->conduitHeaders($this->commenter),
        )
            ->assertUnprocessable()
            ->assertExactJson(['errors' => ['body' => ["can't be blank"]]]);

        $this->assertDatabaseCount('comments', 0);
    }

    public function test_comment_body_cannot_be_too_long(): void
    {
        $this->postJson(
            '/api/articles/'.$this->article->slug.'/comments',
            ['comment' => ['body' => str_repeat('a', 2049)]],
            $this->conduitHeaders($this->commenter),
        )
            ->assertUnprocessable()
            ->assertJsonStructure(['errors' => ['body']]);

        $this->assertDatabaseCount('comments', 0);
    }
}
