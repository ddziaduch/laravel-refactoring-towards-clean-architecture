<?php

namespace Tests\Feature;

use App\Models\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommentContractTest extends TestCase
{
    use RefreshDatabase;

    public function test_comment_creation_listing_authorization_and_scoped_deletion(): void
    {
        $author = $this->createUser('author');
        $commenter = $this->createUser('commenter');
        $stranger = $this->createUser('stranger');
        $article = Article::factory()->for($author)->create(['title' => 'First article']);
        $otherArticle = Article::factory()->for($author)->create(['title' => 'Other article']);
        $commenterHeaders = $this->conduitHeaders($commenter);

        $commentId = $this->postJson('/api/articles/'.$article->slug.'/comments', [
            'comment' => ['body' => 'A useful comment'],
        ], $commenterHeaders)->assertCreated()
            ->assertJsonPath('comment.body', 'A useful comment')
            ->assertJsonPath('comment.author.username', 'commenter')
            ->json('comment.id');

        $this->getJson('/api/articles/'.$article->slug.'/comments')
            ->assertOk()
            ->assertJsonCount(1, 'comments')
            ->assertJsonPath('comments.0.id', $commentId);

        $this->deleteJson('/api/articles/'.$otherArticle->slug.'/comments/'.$commentId, [], $commenterHeaders)
            ->assertNotFound()
            ->assertJsonStructure(['errors' => ['comment']]);

        $this->deleteJson(
            '/api/articles/'.$article->slug.'/comments/'.$commentId,
            [],
            $this->conduitHeaders($stranger),
        )
            ->assertForbidden()
            ->assertJsonStructure(['errors' => ['comment']]);

        $this->deleteJson('/api/articles/'.$article->slug.'/comments/'.$commentId, [], $commenterHeaders)
            ->assertNoContent();

        $this->assertDatabaseMissing('comments', ['id' => $commentId]);
    }
}
