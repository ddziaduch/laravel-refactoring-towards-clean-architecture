<?php

declare(strict_types = 1);

namespace Tests\Feature;

use App\Models\Article;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeleteArticleTest extends TestCase
{
    use RefreshDatabase;

    public function testHappyPath(): void
    {
        $user = User::factory()->create();
        assert($user instanceof User);
        $article = Article::factory()->create(['user_id' => $user->id]);
        assert($article instanceof Article);

        $response = $this->actingAs($user)->deleteJson(sprintf("api/articles/%s", $article->slug));
        $response->assertStatus(200);
    }

    public function testNotFound(): void
    {
        $user = User::factory()->create();
        assert($user instanceof User);

        $response = $this->actingAs($user)->deleteJson(sprintf("api/articles/non-existent"));
        $response->assertStatus(404);
    }
}
