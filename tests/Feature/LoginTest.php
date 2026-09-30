<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_log_in(): void
    {
        $user = $this->createUser('john');

        $response = $this->postJson('/api/users/login', [
            'user' => ['email' => 'john@example.com', 'password' => 'secret'],
        ])->assertOk();

        $response->assertExactJson([
            'user' => [
                'email' => 'john@example.com',
                'token' => $response->json('user.token'),
                'username' => 'john',
                'bio' => $user->bio,
                'image' => $user->image,
            ],
        ]);
    }

    public function test_logged_in_user_can_access_protected_endpoint(): void
    {
        $user = $this->createUser('john');

        $this->getJson('/api/user')
            ->assertUnauthorized()
            ->assertExactJson(['errors' => ['token' => ['is missing']]]);

        $this->getJson('/api/user', $this->conduitHeaders($user))
            ->assertOk()
            ->assertJsonPath('user.username', 'john');
    }
}
