<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_follow_and_unfollow_are_idempotent(): void
    {
        $alice = $this->createUser('alice');
        $bob = $this->createUser('bob');
        $headers = $this->conduitHeaders($alice);

        $this->getJson('/api/profiles/bob')
            ->assertOk()
            ->assertJsonPath('profile.following', false);

        $this->postJson('/api/profiles/bob/follow', [], $headers)
            ->assertOk()
            ->assertJsonPath('profile.following', true);

        $this->postJson('/api/profiles/bob/follow', [], $headers)
            ->assertOk()
            ->assertJsonPath('profile.following', true);

        $this->assertDatabaseCount('followers', 1);

        $this->getJson('/api/profiles/bob', $headers)
            ->assertOk()
            ->assertJsonPath('profile.following', true);

        $this->deleteJson('/api/profiles/bob/follow', [], $headers)
            ->assertOk()
            ->assertJsonPath('profile.following', false);

        $this->deleteJson('/api/profiles/bob/follow', [], $headers)
            ->assertOk()
            ->assertJsonPath('profile.following', false);

        $this->assertDatabaseMissing('followers', [
            'follower_id' => $alice->id,
            'following_id' => $bob->id,
        ]);
    }
}
