<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\User;
use Clean\Application\Port\In\CreateCommentUserCaseInterface;
use Clean\Domain\Exception\ArticleNotFound;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreateCommentTest extends TestCase
{
    use RefreshDatabase;

    public function testHappyPath()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $article = Article::factory()->create();

        $this->assertDatabaseCount('comments', 0);

        $this->postJson('api/articles/' . $article->slug . '/comments', [
            'comment' => [
                'body' => 'test comment',
            ]
        ])
            ->assertSuccessful()
            ->assertJson([
                'comment' => [
                    'body' => 'test comment',
                ]
            ]);

        $this->assertDatabaseHas('comments', [
            'body' => 'test comment',
        ]);
        $this->assertDatabaseCount('comments', 1);
    }

    public function testFail()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $article = Article::factory()->create();

        $response = $this->postJson('api/articles/' . $article->slug . '/comments')
            ->assertStatus(422);
    }

    public function testFailsWhenArticleDoesntExist()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $useCase = $this->createMock(CreateCommentUserCaseInterface::class);
        $useCase->expects($this->once())->method('__invoke')->willThrowException(
            new ArticleNotFound()
        );

        $this->app->instance(CreateCommentUserCaseInterface::class, $useCase);

        $this->postJson('api/articles/blabla/comments', [
            'comment' => [
                'body' => 'test comment',
            ]
        ])
            ->assertStatus(404);
    }
}
