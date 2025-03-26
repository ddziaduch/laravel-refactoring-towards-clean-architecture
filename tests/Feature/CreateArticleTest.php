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
                'slug' => 'test-title',
                'title' => 'test title',
                'description' => 'test description',
                'body' => 'test body',
                'tagList' => ['test', 'tag'],
                'favoritesCount' => 0,
                'favorited' => false,
                'author' => [
                    'username' => $user->username,
                    'bio' => $user->bio,
                    'image' => $user->image,
                    'following' => false,
                ],
            ],
        ]);
        $slug = $postResponse->json()['article']['slug'];

        $this->get('api/articles/' . $slug)->assertStatus(200);
    }
}
