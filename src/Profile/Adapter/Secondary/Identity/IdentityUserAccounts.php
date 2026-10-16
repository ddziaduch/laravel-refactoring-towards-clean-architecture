<?php

namespace Conduit\Profile\Adapter\Secondary\Identity;

use Conduit\Identity\PublicApi\UserDirectory;
use Conduit\Identity\PublicApi\UserSummary;
use Conduit\Profile\Application\Port\Secondary\UserAccount;
use Conduit\Profile\Application\Port\Secondary\UserAccounts;

/**
 * Anti-corruption layer: the only place in the Profile module that knows the
 * Identity module exists. It translates Identity's public DTOs to our own.
 */
final readonly class IdentityUserAccounts implements UserAccounts
{
    public function __construct(private UserDirectory $userDirectory) {}

    public function findByUsername(string $username): ?UserAccount
    {
        $user = $this->userDirectory->findByUsername($username);

        return $user === null ? null : $this->translate($user);
    }

    public function findByIds(array $ids): array
    {
        return array_map($this->translate(...), $this->userDirectory->findByIds($ids));
    }

    private function translate(UserSummary $user): UserAccount
    {
        return new UserAccount($user->id, $user->username, $user->bio, $user->image);
    }
}
