<?php

namespace Tests\Feature\Profile;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_can_be_shown(): void
    {
        $bob = $this->registerUser('bob');
        $this->putJson('/api/user', ['user' => ['bio' => 'Bob bio']], $bob->headers())->assertOk();

        $this->getJson('/api/profiles/bob')
            ->assertOk()
            ->assertExactJson([
                'profile' => [
                    'username' => 'bob',
                    'bio' => 'Bob bio',
                    'image' => null,
                    'following' => false,
                ],
            ]);
    }

    public function test_missing_profile_is_not_found(): void
    {
        $this->getJson('/api/profiles/nobody')
            ->assertNotFound()
            ->assertExactJson(['errors' => ['profile' => ['not found']]]);

        $alice = $this->registerUser('alice');

        $this->postJson('/api/profiles/nobody/follow', [], $alice->headers())
            ->assertNotFound()
            ->assertExactJson(['errors' => ['profile' => ['not found']]]);
    }

    public function test_following_requires_a_token(): void
    {
        $this->registerUser('bob');

        $this->postJson('/api/profiles/bob/follow')
            ->assertUnauthorized()
            ->assertExactJson(['errors' => ['token' => ['is missing']]]);

        $this->deleteJson('/api/profiles/bob/follow')
            ->assertUnauthorized()
            ->assertExactJson(['errors' => ['token' => ['is missing']]]);
    }

    public function test_follow_and_unfollow_are_idempotent(): void
    {
        $alice = $this->registerUser('alice');
        $bob = $this->registerUser('bob');

        $this->postJson('/api/profiles/bob/follow', [], $alice->headers())
            ->assertOk()
            ->assertExactJson([
                'profile' => [
                    'username' => 'bob',
                    'bio' => null,
                    'image' => null,
                    'following' => true,
                ],
            ]);

        $this->postJson('/api/profiles/bob/follow', [], $alice->headers())
            ->assertOk()
            ->assertJsonPath('profile.following', true);

        $this->assertDatabaseCount('followers', 1);

        $this->getJson('/api/profiles/bob', $alice->headers())->assertJsonPath('profile.following', true);
        $this->getJson('/api/profiles/bob')->assertJsonPath('profile.following', false);
        $this->getJson('/api/profiles/alice', $bob->headers())->assertJsonPath('profile.following', false);

        $this->deleteJson('/api/profiles/bob/follow', [], $alice->headers())
            ->assertOk()
            ->assertJsonPath('profile.following', false);

        $this->deleteJson('/api/profiles/bob/follow', [], $alice->headers())
            ->assertOk()
            ->assertJsonPath('profile.following', false);

        $this->assertDatabaseCount('followers', 0);
    }
}
