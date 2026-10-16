<?php

namespace Tests\Feature\Identity;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CurrentUserTest extends TestCase
{
    use RefreshDatabase;

    public function test_current_user_requires_a_token(): void
    {
        $this->getJson('/api/user')
            ->assertUnauthorized()
            ->assertExactJson(['errors' => ['token' => ['is missing']]]);

        $this->putJson('/api/user', ['user' => ['bio' => 'Bio']])
            ->assertUnauthorized()
            ->assertExactJson(['errors' => ['token' => ['is missing']]]);
    }

    public function test_current_user_is_returned_with_the_same_token(): void
    {
        $john = $this->registerUser('john');

        $this->getJson('/api/user', $john->headers())
            ->assertOk()
            ->assertExactJson([
                'user' => [
                    'email' => 'john@example.com',
                    'token' => $john->token,
                    'username' => 'john',
                    'bio' => null,
                    'image' => null,
                ],
            ]);
    }

    public function test_current_user_can_be_updated(): void
    {
        $john = $this->registerUser('john');

        $this->putJson('/api/user', [
            'user' => [
                'username' => 'johnny',
                'email' => 'johnny@example.com',
                'bio' => 'A new bio',
                'image' => 'https://example.com/johnny.png',
                'password' => 'another-password',
            ],
        ], $john->headers())
            ->assertOk()
            ->assertExactJson([
                'user' => [
                    'email' => 'johnny@example.com',
                    'token' => $john->token,
                    'username' => 'johnny',
                    'bio' => 'A new bio',
                    'image' => 'https://example.com/johnny.png',
                ],
            ]);

        $this->postJson('/api/users/login', [
            'user' => ['email' => 'johnny@example.com', 'password' => 'another-password'],
        ])->assertOk();

        $this->getJson('/api/profiles/johnny')
            ->assertOk()
            ->assertJsonPath('profile.bio', 'A new bio');
    }

    public function test_current_user_can_keep_unique_values_and_clear_nullable_fields(): void
    {
        $alice = $this->registerUser('alice');

        $this->putJson('/api/user', [
            'user' => ['bio' => 'Bio', 'image' => 'https://example.com/a.png'],
        ], $alice->headers())->assertOk();

        $this->putJson('/api/user', [
            'user' => [
                'username' => 'alice',
                'email' => 'alice@example.com',
                'bio' => null,
                'image' => null,
            ],
        ], $alice->headers())
            ->assertOk()
            ->assertJsonPath('user.username', 'alice')
            ->assertJsonPath('user.bio', null)
            ->assertJsonPath('user.image', null);
    }

    public function test_current_user_cannot_take_someone_elses_username(): void
    {
        $this->registerUser('bob');
        $alice = $this->registerUser('alice');

        $this->putJson('/api/user', ['user' => ['username' => 'bob']], $alice->headers())
            ->assertUnprocessable()
            ->assertExactJson(['errors' => ['username' => ['has already been taken']]]);
    }
}
