<?php

namespace Tests\Feature\Engagement;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;
use Tests\TestUser;

class CommentsTest extends TestCase
{
    use RefreshDatabase;

    private TestUser $author;

    private TestUser $commenter;

    private string $slug;

    protected function setUp(): void
    {
        parent::setUp();

        // Tokens are issued at the travelled time, so every test may only travel forward.
        $this->travelTo(Carbon::parse('2026-10-01 12:00:00'));

        $this->author = $this->registerUser('author');
        $this->commenter = $this->registerUser('commenter');
        $this->slug = $this->publishArticle($this->author, 'Commented article');
    }

    public function test_user_can_comment_an_article(): void
    {
        $this->follow($this->author, $this->commenter);
        $this->travelTo(Carbon::parse('2026-10-01 12:34:56'));

        $response = $this->postJson(
            '/api/articles/'.$this->slug.'/comments',
            ['comment' => ['body' => 'A useful comment']],
            $this->commenter->headers(),
        )->assertCreated();

        $response->assertExactJson([
            'comment' => [
                'id' => $response->json('comment.id'),
                'createdAt' => '2026-10-01T12:34:56.000000Z',
                'updatedAt' => '2026-10-01T12:34:56.000000Z',
                'body' => 'A useful comment',
                'author' => [
                    'username' => 'commenter',
                    'bio' => null,
                    'image' => null,
                    'following' => false,
                ],
            ],
        ]);

        $this->assertDatabaseHas('comments', [
            'id' => $response->json('comment.id'),
            'user_id' => $this->commenter->id,
            'body' => 'A useful comment',
        ]);
    }

    public function test_guest_cannot_comment_an_article(): void
    {
        $this->postJson('/api/articles/'.$this->slug.'/comments', ['comment' => ['body' => 'A useful comment']])
            ->assertUnauthorized()
            ->assertExactJson(['errors' => ['token' => ['is missing']]]);

        $this->assertDatabaseCount('comments', 0);
    }

    public function test_user_cannot_comment_a_missing_article(): void
    {
        $this->postJson(
            '/api/articles/missing-article/comments',
            ['comment' => ['body' => 'A useful comment']],
            $this->commenter->headers(),
        )
            ->assertNotFound()
            ->assertExactJson(['errors' => ['article' => ['not found']]]);

        $this->assertDatabaseCount('comments', 0);
    }

    public function test_comment_body_is_validated(): void
    {
        $this->postJson(
            '/api/articles/'.$this->slug.'/comments',
            ['comment' => ['body' => '']],
            $this->commenter->headers(),
        )
            ->assertUnprocessable()
            ->assertExactJson(['errors' => ['body' => ["can't be blank"]]]);

        $this->postJson(
            '/api/articles/'.$this->slug.'/comments',
            ['comment' => ['body' => str_repeat('a', 2049)]],
            $this->commenter->headers(),
        )
            ->assertUnprocessable()
            ->assertJsonStructure(['errors' => ['body']]);

        $this->assertDatabaseCount('comments', 0);
    }

    public function test_comments_are_listed_from_the_perspective_of_a_viewer(): void
    {
        $first = $this->comment($this->commenter, $this->slug, 'First');
        $second = $this->comment($this->author, $this->slug, 'Second');
        $otherSlug = $this->publishArticle($this->author, 'Other article');
        $this->comment($this->commenter, $otherSlug, 'Elsewhere');
        $this->follow($this->author, $this->commenter);

        $this->getJson('/api/articles/'.$this->slug.'/comments')
            ->assertOk()
            ->assertJsonCount(2, 'comments')
            ->assertJsonPath('comments.*.id', [$first, $second])
            ->assertJsonPath('comments.*.author.following', [false, false])
            ->assertJsonStructure(['comments' => [['id', 'createdAt', 'updatedAt', 'body', 'author']]]);

        $this->getJson('/api/articles/'.$this->slug.'/comments', $this->author->headers())
            ->assertOk()
            ->assertJsonPath('comments.0.author.username', 'commenter')
            ->assertJsonPath('comments.0.author.following', true);
    }

    public function test_comments_of_a_missing_article_are_not_found(): void
    {
        $this->getJson('/api/articles/missing/comments')
            ->assertNotFound()
            ->assertExactJson(['errors' => ['article' => ['not found']]]);
    }

    public function test_comment_author_can_delete_a_comment(): void
    {
        $commentId = $this->comment($this->commenter, $this->slug, 'To be deleted');

        $this->deleteJson('/api/articles/'.$this->slug.'/comments/'.$commentId)
            ->assertUnauthorized();

        $this->deleteJson('/api/articles/'.$this->slug.'/comments/'.$commentId, [], $this->author->headers())
            ->assertForbidden()
            ->assertExactJson(['errors' => ['comment' => ['forbidden']]]);

        $this->deleteJson('/api/articles/'.$this->slug.'/comments/'.$commentId, [], $this->commenter->headers())
            ->assertNoContent();

        $this->assertDatabaseMissing('comments', ['id' => $commentId]);
    }

    public function test_comment_deletion_is_scoped_to_the_article(): void
    {
        $commentId = $this->comment($this->commenter, $this->slug, 'Scoped');
        $otherSlug = $this->publishArticle($this->author, 'Other article');

        $this->deleteJson('/api/articles/'.$otherSlug.'/comments/'.$commentId, [], $this->commenter->headers())
            ->assertNotFound()
            ->assertExactJson(['errors' => ['comment' => ['not found']]]);

        $this->deleteJson('/api/articles/'.$this->slug.'/comments/999999', [], $this->commenter->headers())
            ->assertNotFound()
            ->assertExactJson(['errors' => ['comment' => ['not found']]]);

        $this->deleteJson('/api/articles/missing/comments/'.$commentId, [], $this->commenter->headers())
            ->assertNotFound()
            ->assertExactJson(['errors' => ['article' => ['not found']]]);

        $this->assertDatabaseHas('comments', ['id' => $commentId]);
    }
}
