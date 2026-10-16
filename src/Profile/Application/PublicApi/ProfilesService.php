<?php

namespace Conduit\Profile\Application\PublicApi;

use Conduit\Profile\Application\Port\Secondary\FollowRepository;
use Conduit\Profile\Application\Port\Secondary\UserAccounts;
use Conduit\Profile\Application\ReadModel\ProfileReadModel;
use Conduit\Profile\Application\Service\ProfileFinder;
use Conduit\Profile\PublicApi\FollowGraph;
use Conduit\Profile\PublicApi\Profiles;
use Conduit\Profile\PublicApi\ProfileView;

final readonly class ProfilesService implements FollowGraph, Profiles
{
    public function __construct(
        private ProfileFinder $profiles,
        private UserAccounts $userAccounts,
        private FollowRepository $follows,
    ) {}

    public function profilesOf(array $userIds, ?int $viewerId): array
    {
        return array_map(
            fn (ProfileReadModel $profile): ProfileView => new ProfileView(
                $profile->userId,
                $profile->username,
                $profile->bio,
                $profile->image,
                $profile->following,
            ),
            $this->profiles->profilesOf($userIds, $viewerId),
        );
    }

    public function userIdOf(string $username): ?int
    {
        return $this->userAccounts->findByUsername($username)?->id;
    }

    public function followedBy(int $userId): array
    {
        return $this->follows->followedBy($userId);
    }
}
