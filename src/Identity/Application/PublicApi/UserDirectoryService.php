<?php

namespace Conduit\Identity\Application\PublicApi;

use Conduit\Identity\Application\Port\Secondary\UserRepository;
use Conduit\Identity\Domain\User;
use Conduit\Identity\PublicApi\UserDirectory;
use Conduit\Identity\PublicApi\UserSummary;

/**
 * Implements the module's published contract on top of its own ports.
 * Domain objects are translated to public DTOs here and never leave the module.
 */
final readonly class UserDirectoryService implements UserDirectory
{
    public function __construct(private UserRepository $users) {}

    public function findByUsername(string $username): ?UserSummary
    {
        $user = $this->users->findByUsername($username);

        return $user === null ? null : $this->summarize($user);
    }

    public function findByIds(array $ids): array
    {
        $summaries = [];

        foreach ($this->users->findMany($ids) as $user) {
            $summaries[$user->id] = $this->summarize($user);
        }

        return $summaries;
    }

    private function summarize(User $user): UserSummary
    {
        return new UserSummary($user->id, $user->username->value, $user->bio, $user->image);
    }
}
