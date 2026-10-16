<?php

namespace Conduit\Article\Adapter\Secondary\Profile;

use Conduit\Article\Application\Port\Secondary\AuthorProfiles;
use Conduit\Article\Application\Port\Secondary\Followings;
use Conduit\Article\Application\ReadModel\AuthorReadModel;
use Conduit\Profile\PublicApi\FollowGraph;
use Conduit\Profile\PublicApi\Profiles;
use Conduit\Profile\PublicApi\ProfileView;

/**
 * Anti-corruption layer: the Article module talks to the Profile module only here,
 * and only through its public API.
 */
final readonly class ProfileAuthors implements AuthorProfiles, Followings
{
    public function __construct(
        private Profiles $profiles,
        private FollowGraph $followGraph,
    ) {}

    public function of(array $authorIds, ?int $viewerId): array
    {
        return array_map(
            fn (ProfileView $profile): AuthorReadModel => new AuthorReadModel(
                $profile->username,
                $profile->bio,
                $profile->image,
                $profile->following,
            ),
            $this->profiles->profilesOf($authorIds, $viewerId),
        );
    }

    public function idOfUsername(string $username): ?int
    {
        return $this->profiles->userIdOf($username);
    }

    public function followedBy(int $readerId): array
    {
        return $this->followGraph->followedBy($readerId);
    }
}
