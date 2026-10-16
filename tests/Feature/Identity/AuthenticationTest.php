<?php

namespace Tests\Feature\Identity;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register(): void
    {
        $response = $this->postJson('/api/users', [
            'user' => [
                'username' => 'alice',
                'email' => 'alice@example.com',
                'password' => 'password123',
            ],
        ])->assertCreated();

        $response->assertExactJson([
            'user' => [
                'email' => 'alice@example.com',
                'token' => $response->json('user.token'),
                'username' => 'alice',
                'bio' => null,
                'image' => null,
            ],
        ]);
        $this->assertIsString($response->json('user.token'));

        $this->getJson('/api/user', ['Authorization' => 'Token '.$response->json('user.token')])
            ->assertOk()
            ->assertJsonPath('user.username', 'alice');
    }

    public function test_duplicate_registration_is_a_conflict(): void
    {
        $this->registerUser('alice');

        $this->postJson('/api/users', [
            'user' => [
                'username' => 'alice',
                'email' => 'alice@example.com',
                'password' => 'password123',
            ],
        ])
            ->assertConflict()
            ->assertExactJson([
                'errors' => [
                    'username' => ['has already been taken'],
                    'email' => ['has already been taken'],
                ],
            ]);
    }

    public function test_registration_is_validated(): void
    {
        $this->postJson('/api/users', ['user' => []])
            ->assertUnprocessable()
            ->assertExactJson([
                'errors' => [
                    'user' => ["can't be blank"],
                    'username' => ["can't be blank"],
                    'email' => ["can't be blank"],
                    'password' => ["can't be blank"],
                ],
            ]);

        $this->postJson('/api/users', [
            'user' => ['username' => 'alice', 'email' => 'not-an-email', 'password' => 'short'],
        ])
            ->assertUnprocessable()
            ->assertJsonStructure(['errors' => ['email', 'password']])
            ->assertJsonMissingPath('message');
    }

    public function test_user_can_log_in(): void
    {
        $this->registerUser('john', 'secret-password');

        $response = $this->postJson('/api/users/login', [
            'user' => ['email' => 'john@example.com', 'password' => 'secret-password'],
        ])->assertOk();

        $response->assertExactJson([
            'user' => [
                'email' => 'john@example.com',
                'token' => $response->json('user.token'),
                'username' => 'john',
                'bio' => null,
                'image' => null,
            ],
        ]);
    }

    public function test_login_with_invalid_credentials_is_rejected(): void
    {
        $this->registerUser('john', 'secret-password');

        $this->postJson('/api/users/login', [
            'user' => ['email' => 'john@example.com', 'password' => 'wrong-password'],
        ])
            ->assertUnauthorized()
            ->assertExactJson(['errors' => ['credentials' => ['invalid']]]);

        $this->postJson('/api/users/login', [
            'user' => ['email' => 'nobody@example.com', 'password' => 'secret-password'],
        ])
            ->assertUnauthorized()
            ->assertExactJson(['errors' => ['credentials' => ['invalid']]]);
    }

    public function test_login_is_validated(): void
    {
        $this->postJson('/api/users/login', ['user' => ['email' => 'john@example.com']])
            ->assertUnprocessable()
            ->assertExactJson(['errors' => ['password' => ["can't be blank"]]]);
    }
}
