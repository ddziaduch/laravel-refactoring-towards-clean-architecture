<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_registration_login_and_current_user_follow_the_contract(): void
    {
        $registration = $this->postJson('/api/users', [
            'user' => [
                'username' => 'alice',
                'email' => 'alice@example.com',
                'password' => 'password123',
            ],
        ])->assertCreated()
            ->assertJsonStructure([
                'user' => ['username', 'email', 'bio', 'image', 'token'],
            ])
            ->assertJsonPath('user.username', 'alice')
            ->assertJsonPath('user.bio', null)
            ->assertJsonPath('user.image', null);

        $token = $registration->json('user.token');

        $this->getJson('/api/user')
            ->assertUnauthorized()
            ->assertExactJson(['errors' => ['token' => ['is missing']]]);

        $this->getJson('/api/user', ['Authorization' => 'Token '.$token])
            ->assertOk()
            ->assertJsonPath('user.email', 'alice@example.com');

        $this->postJson('/api/users/login', [
            'user' => ['email' => 'alice@example.com', 'password' => 'password123'],
        ])->assertOk()
            ->assertJsonStructure(['user' => ['token']]);

        $this->postJson('/api/users/login', [
            'user' => ['email' => 'alice@example.com', 'password' => 'wrong'],
        ])->assertUnauthorized()
            ->assertExactJson(['errors' => ['credentials' => ['invalid']]]);
    }

    public function test_duplicate_registration_is_a_conflict_and_validation_has_conduit_shape(): void
    {
        $payload = [
            'user' => [
                'username' => 'alice',
                'email' => 'alice@example.com',
                'password' => 'password123',
            ],
        ];

        $this->postJson('/api/users', $payload)->assertCreated();

        $this->postJson('/api/users', $payload)
            ->assertConflict()
            ->assertJsonStructure(['errors' => ['username', 'email']])
            ->assertJsonMissingPath('message');

        $this->postJson('/api/users', ['user' => []])
            ->assertUnprocessable()
            ->assertJsonStructure(['errors' => ['username', 'email', 'password']])
            ->assertJsonMissingPath('message');
    }

    public function test_current_user_can_keep_unique_values_and_clear_nullable_fields(): void
    {
        $user = $this->createUser('alice');

        $this->putJson('/api/user', [
            'user' => [
                'username' => 'alice',
                'email' => 'alice@example.com',
                'bio' => null,
                'image' => null,
            ],
        ], $this->conduitHeaders($user))->assertOk()
            ->assertJsonPath('user.username', 'alice')
            ->assertJsonPath('user.bio', null)
            ->assertJsonPath('user.image', null);
    }
}
