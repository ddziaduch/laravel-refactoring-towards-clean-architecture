<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\User;
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
            ->assertSuccessful();

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
}
