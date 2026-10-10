<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\Comment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeleteCommentTest extends TestCase
{
    use RefreshDatabase;

    private User $commenter;

    private Article $article;

    private Comment $comment;

    protected function setUp(): void
    {
        parent::setUp();

        $this->commenter = $this->createUser('commenter');
        $this->article = Article::factory()->for($this->createUser('author'))->create();
        $this->comment = Comment::factory()->create([
            'article_id' => $this->article->id,
            'user_id' => $this->commenter->id,
        ]);
    }

    public function test_author_can_delete_their_comment(): void
    {
        $this->deleteJson($this->commentUri(), [], $this->conduitHeaders($this->commenter))
            ->assertNoContent();

        $this->getJson('/api/articles/'.$this->article->slug.'/comments')
            ->assertOk()
            ->assertExactJson(['comments' => []]);

        $this->deleteJson($this->commentUri(), [], $this->conduitHeaders($this->commenter))
            ->assertNotFound()
            ->assertExactJson(['errors' => ['comment' => ['not found']]]);
    }

    public function test_each_request_deletes_as_its_own_user(): void
    {
        $this->deleteJson($this->commentUri(), [], $this->conduitHeaders($this->createUser('stranger')))
            ->assertForbidden();

        $this->deleteJson($this->commentUri(), [], $this->conduitHeaders($this->commenter))
            ->assertNoContent();
    }

    public function test_guest_cannot_delete_a_comment(): void
    {
        $this->deleteJson($this->commentUri())
            ->assertUnauthorized()
            ->assertExactJson(['errors' => ['token' => ['is missing']]]);

        $this->assertCommentIsStillListed();
    }

    public function test_user_cannot_delete_a_comment_of_someone_else(): void
    {
        $this->deleteJson($this->commentUri(), [], $this->conduitHeaders($this->createUser('stranger')))
            ->assertForbidden()
            ->assertExactJson(['errors' => ['comment' => ['forbidden']]]);

        $this->assertCommentIsStillListed();
    }

    public function test_user_cannot_delete_a_comment_of_a_missing_article(): void
    {
        $this->deleteJson(
            '/api/articles/missing-article/comments/'.$this->comment->id,
            [],
            $this->conduitHeaders($this->commenter),
        )
            ->assertNotFound()
            ->assertExactJson(['errors' => ['article' => ['not found']]]);

        $this->assertCommentIsStillListed();
    }

    public function test_user_cannot_delete_a_missing_comment(): void
    {
        $this->deleteJson(
            '/api/articles/'.$this->article->slug.'/comments/'.($this->comment->id + 1),
            [],
            $this->conduitHeaders($this->commenter),
        )
            ->assertNotFound()
            ->assertExactJson(['errors' => ['comment' => ['not found']]]);

        $this->assertCommentIsStillListed();
    }

    public function test_user_cannot_delete_a_comment_through_another_article(): void
    {
        $otherArticle = Article::factory()->for($this->createUser('other-author'))->create();

        $this->deleteJson(
            '/api/articles/'.$otherArticle->slug.'/comments/'.$this->comment->id,
            [],
            $this->conduitHeaders($this->commenter),
        )
            ->assertNotFound()
            ->assertExactJson(['errors' => ['comment' => ['not found']]]);

        $this->assertCommentIsStillListed();
    }

    private function commentUri(): string
    {
        return '/api/articles/'.$this->article->slug.'/comments/'.$this->comment->id;
    }

    private function assertCommentIsStillListed(): void
    {
        $this->getJson('/api/articles/'.$this->article->slug.'/comments')
            ->assertOk()
            ->assertJsonCount(1, 'comments')
            ->assertJsonPath('comments.0.id', $this->comment->id);
    }
}
