<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreateCommentTest extends TestCase
{
    use RefreshDatabase;

    public function testShowArticle()
    {
        $user = User::factory()->create();
        assert($user instanceof User);

        $article = Article::factory()->create();
        assert($article instanceof Article);

        $response = $this->actingAs($user)->postJson(
            sprintf('api/articles/%s/comments', $article->slug),
            [
                'comment' => [
                    'body' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
                ],
            ],
        );
        $response->assertStatus(201);
    }
}
