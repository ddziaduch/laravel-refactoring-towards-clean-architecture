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

        $this->actingAs($user)->deleteJson(sprintf("api/articles/%s", $article->slug))->assertStatus(200);
        $this->actingAs($user)->getJson(sprintf("api/articles/%s", $article->slug))->assertStatus(404);
        $this->actingAs($user)->deleteJson(sprintf("api/articles/%s", $article->slug))->assertStatus(404);
    }

    public function testNotFound(): void
    {
        $user = User::factory()->create();
        assert($user instanceof User);

        $response = $this->actingAs($user)->deleteJson(sprintf("api/articles/non-existent"));
        $response->assertStatus(404);
    }
}
