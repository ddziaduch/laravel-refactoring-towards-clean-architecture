<?php

namespace Conduit\Engagement\Adapter\Secondary\Profile;

use Conduit\Engagement\Application\Port\Secondary\AuthorProfiles;
use Conduit\Engagement\Application\ReadModel\AuthorReadModel;
use Conduit\Profile\PublicApi\Profiles;
use Conduit\Profile\PublicApi\ProfileView;

/**
 * Anti-corruption layer towards the Profile module.
 */
final readonly class ProfileAuthors implements AuthorProfiles
{
    public function __construct(private Profiles $profiles) {}

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
}
