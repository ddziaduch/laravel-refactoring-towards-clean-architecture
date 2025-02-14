<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreateArticleTest extends TestCase
{
    use RefreshDatabase;

    public function testHappyPath()
    {
        $user = User::factory()->create();
        assert($user instanceof User);
        $this->actingAs($user);

        $postResponse = $this->postJson('api/articles', [
            'article' => [
                'title' => 'test title',
                'body' => 'test body',
                'description' => 'test description',
                'tagList' => ['test', 'tag'],
            ],
        ]);
        $postResponse->assertStatus(201)->assertJson([
            'article' => [
                'title' => 'test title',
                'body' => 'test body',
                'description' => 'test description',
                'tagList' => ['test', 'tag'],
            ],
        ]);
        $slug = $postResponse->json()['article']['slug'];

        $this->get('api/articles/' . $slug)->assertStatus(200);
    }
}
