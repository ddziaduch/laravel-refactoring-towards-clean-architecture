<?php

namespace Conduit\Profile\Application\Port\Secondary;

/**
 * Users as the Profile module needs to see them. Implemented by an adapter
 * on top of another module, the use cases do not know which one.
 */
interface UserAccounts
{
    public function findByUsername(string $username): ?UserAccount;

    /**
     * @param  list<int>  $ids
     * @return array<int, UserAccount> keyed by user id
     */
    public function findByIds(array $ids): array;
}
