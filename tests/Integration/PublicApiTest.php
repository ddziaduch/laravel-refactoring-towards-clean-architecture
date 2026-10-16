<?php

namespace Tests\Integration;

use Conduit\Article\PublicApi\Articles;
use Conduit\Identity\PublicApi\UserDirectory;
use Conduit\Identity\PublicApi\UserSummary;
use Conduit\Profile\PublicApi\FollowGraph;
use Conduit\Profile\PublicApi\Profiles;
use Conduit\Profile\PublicApi\ProfileView;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Provider-side contract tests: each module's published API, resolved from the
 * container exactly as other modules get it.
 */
class PublicApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_identity_user_directory(): void
    {
        $alice = $this->registerUser('alice');
        $bob = $this->registerUser('bob');
        $directory = $this->app->make(UserDirectory::class);

        $this->assertEquals(new UserSummary($alice->id, 'alice', null, null), $directory->findByUsername('alice'));
        $this->assertNull($directory->findByUsername('nobody'));
        $this->assertEqualsCanonicalizing(
            [$alice->id, $bob->id],
            array_keys($directory->findByIds([$alice->id, $bob->id, 999999])),
        );
        $this->assertSame([], $directory->findByIds([]));
    }

    public function test_profile_profiles_and_follow_graph(): void
    {
        $alice = $this->registerUser('alice');
        $bob = $this->registerUser('bob');
        $this->follow($alice, $bob);

        $profiles = $this->app->make(Profiles::class);

        $this->assertEquals(
            [
                $alice->id => new ProfileView($alice->id, 'alice', null, null, false),
                $bob->id => new ProfileView($bob->id, 'bob', null, null, true),
            ],
            $profiles->profilesOf([$alice->id, $bob->id], $alice->id),
        );
        $this->assertFalse($profiles->profilesOf([$bob->id], null)[$bob->id]->following);
        $this->assertSame($bob->id, $profiles->userIdOf('bob'));
        $this->assertNull($profiles->userIdOf('nobody'));
        $this->assertSame([$bob->id], $this->app->make(FollowGraph::class)->followedBy($alice->id));
        $this->assertSame([], $this->app->make(FollowGraph::class)->followedBy($bob->id));
    }

    public function test_article_articles(): void
    {
        $slug = $this->publishArticle($this->registerUser('author'), 'Public article');
        $articles = $this->app->make(Articles::class);

        $this->assertIsInt($articles->idOfSlug($slug));
        $this->assertNull($articles->idOfSlug('missing'));
    }
}
