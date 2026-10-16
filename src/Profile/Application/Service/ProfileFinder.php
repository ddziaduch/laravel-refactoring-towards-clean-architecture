<?php

namespace Conduit\Profile\Application\Service;

use Conduit\Profile\Application\Exception\ProfileNotFound;
use Conduit\Profile\Application\Port\Secondary\FollowRepository;
use Conduit\Profile\Application\Port\Secondary\UserAccount;
use Conduit\Profile\Application\Port\Secondary\UserAccounts;
use Conduit\Profile\Application\ReadModel\ProfileReadModel;

/**
 * Builds profiles from the viewer's perspective, shared by the use cases
 * and by the module's public API.
 */
final readonly class ProfileFinder
{
    public function __construct(
        private UserAccounts $userAccounts,
        private FollowRepository $follows,
    ) {}

    /**
     * @throws ProfileNotFound
     */
    public function accountOf(string $username): UserAccount
    {
        return $this->userAccounts->findByUsername($username)
            ?? throw ProfileNotFound::withUsername($username);
    }

    public function profileOf(UserAccount $account, ?int $viewerId): ProfileReadModel
    {
        return $this->profilesOf([$account->id], $viewerId)[$account->id];
    }

    /**
     * @param  list<int>  $userIds
     * @return array<int, ProfileReadModel> keyed by user id
     */
    public function profilesOf(array $userIds, ?int $viewerId): array
    {
        $accounts = $this->userAccounts->findByIds($userIds);
        $followed = $viewerId === null
            ? []
            : $this->follows->followedAmong($viewerId, array_keys($accounts));

        $profiles = [];

        foreach ($accounts as $id => $account) {
            $profiles[$id] = new ProfileReadModel(
                $account->id,
                $account->username,
                $account->bio,
                $account->image,
                in_array($id, $followed, true),
            );
        }

        return $profiles;
    }
}
