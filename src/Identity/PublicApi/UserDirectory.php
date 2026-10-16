<?php

namespace Conduit\Identity\PublicApi;

/**
 * What other modules may know about users. Nothing about credentials leaks out.
 */
interface UserDirectory
{
    public function findByUsername(string $username): ?UserSummary;

    /**
     * Batch lookup, so callers never have to query user by user.
     *
     * @param  list<int>  $ids
     * @return array<int, UserSummary> keyed by user id, unknown ids are skipped
     */
    public function findByIds(array $ids): array;
}
